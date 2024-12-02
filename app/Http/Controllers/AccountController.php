<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\PersonalInfo;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            'occupation' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'gender' => 'required|string|max:50', // Gender usually doesn't need 255 characters
            'contact_number' => 'required|string|max:15', // Phone numbers typically have a shorter max length
            'birthdate' => 'required|date', // Validate as a valid date
            'type' => 'required|string|max:50', // Adjusted to string unless it's really an email
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
                'occupation' => $request->occupation,
                'role' => $request->role,
                'gender' => $request->gender,
                'contact_number' => $request->contact_number,
                'birthdate' => $request->birthdate,
                'username' => $request->username,
                'type' => $request->type,
                'email' => $request->email,
            ]);

            $account = Account::create([
                'personal_id' => $personalInformation->id,  // Assuming a relationship between the two tables
                'fullname' => $request->first_name . ' ' . $request->last_name,
                'type' =>  $request->type,
                'email' => $request->email,
                'email_verified_at' => "-",
                'password' => Hash::make($request->password), // Hash the password for security
            ]);

            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'User registered successfully.',
                'data' => [
                    'Info' => $personalInformation,
                    'account' => $account
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
        $account = Account::where('email', '=', $request->email)->first();
        if ($account) {
            if (Hash::check($request->password, $account->password)) {
                if ($account->is_verify == 1) {
                    $token = $account->createToken($request->email)->plainTextToken;

                    return response()->json([
                        'message' => 'Authorized!',
                        'status' => 1,
                        'token' => $token,
                        'data' => $account
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Your account is still not verify, Thank you!',
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
                'status' => 0
            ]);
        }
    }
}
