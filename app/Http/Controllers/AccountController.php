<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\PersonalInfo;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Certificate;
use App\Models\Artist;

class AccountController extends Controller
{
    public function register(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'main_address' => 'required|string|max:255',
            'sub_address' => 'nullable|string|max:255', // Sub address can be optional
            'role' => 'required|string|max:255',
            'gender' => 'required|string|max:50', // Gender usually doesn't need 255 characters
            'contact_number' => 'required|string|max:15|unique:personal_infos,contact_number', // Phone numbers typically have a shorter max length
            'birthdate' => 'required|date', // Validate as a valid date
            'email' => 'required|email|unique:personal_infos,email', // Added email field explicitly
        ]);


        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create new personal information record
            $personalInformation = PersonalInfo::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'main_address' => $request->main_address,
                'sub_address' => $request->sub_address,
                'occupation' => $request->role == 'Artist' ? $request->occupation : "Client",
                'role' => $request->role,
                'gender' => $request->gender,
                'contact_number' => $request->contact_number,
                'birthdate' => $request->birthdate,
                'username' => $request->username,
                'type' => $request->role == 'Artist' ?  $request->type : "Client",
                'email' => $request->email,
            ]);

            $account = Account::create([
                'personal_id' => $personalInformation->id,  // Assuming a relationship between the two tables
                'fullname' => $request->first_name . ' ' . $request->last_name,
                'type' =>  $request->role == 'Artist' ?  $request->type : "Client",
                'email' => $request->email,
                'email_verified_at' => "-",
                'password' => Hash::make($request->password), // Hash the password for security
            ]);

            if ($request->role == 'Artist') { //if role is artist
                $artist = Artist::create([
                    'personal_id' => $personalInformation->id,  // Assuming a relationship between the two tables
                    'acc_id' => $account->id,
                    'full_name' => $request->first_name . ' ' . $request->last_name,
                    'price_range' => 00.00,
                    'occupation' => $request->occupation,
                ]);
            }



            $certificateFilePath = null;
            if ($request->hasFile('certificate_file')) {
                $certificateFile = $request->file('certificate_file');
                $certificateFilePath = $certificateFile->store('certificates', 'public'); // Store in the 'public/certificates' directory
            }
            // Save file path to certificates table
            if ($certificateFilePath) {
                Certificate::create([
                    'acc_id' => $account->id, // Assuming acc_id links to the account ID
                    'file_path' => $certificateFilePath,
                ]);
            }
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'User registered successfully.',
                'data' => [
                    'Info' => $personalInformation,
                    'account' => $account,
                    'certificate' => $certificateFilePath
                ],
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            // Rollback on error
            DB::rollBack();

            return response()->json([
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // Validate the email and password
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find the account by email
        $account = Account::where('email', '=', $request->email)->first();
        if ($account) {
            // Check if the password matches
            if (Hash::check($request->password, $account->password)) {
                if ($account->is_verify == 1) {
                    $token = $account->createToken($request->email)->plainTextToken;
                    $account->load(['personalInfo', 'profilePhoto']);
                    return response()->json([
                        'message' => 'Successful Logged In!',
                        'status' => 1,
                        'token' => $token,
                        'data' => $account
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Your account is not yet verified. Please wait for the verification process to complete. Thank you for your patience!',
                        'status' => -2
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Wrong Credentials!',
                    'status' => -1
                ]);
            }
        } else {
            return response()->json([
                'message' => 'No Match Record!',
                'status' => -3
            ]);
        }
    }
}
