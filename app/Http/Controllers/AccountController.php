<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationEmail;
use App\Mail\RejectEmail;
use App\Mail\Verified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\PersonalInfo;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Certificate;
use App\Models\Artist;
use App\Models\ClientID;

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
            'email' => 'required|email', // Added email field explicitly
            'password' => 'required|string|min:8', // Password validation with confirmation
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the email already exists
            $existingAccount = Account::where('email', '=', $request->email)->latest()
                ->first();

            if ($existingAccount) {
                // If account exists, check if it's rejected (is_cancel == 1) or awaiting verification (is_verify == 0)
                if ($existingAccount->is_cancel == 0 && $existingAccount->is_verify == 0) {
                    // Email is already registered and under verification
                    return response()->json([
                        'message' => 'This email is already registered and awaiting verification.',
                        'status' => 200,
                    ], 200);
                } elseif ($existingAccount->is_cancel == 1) {
                    // If no account is found with the same email, proceed with registration
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
                        'type' => $request->role == 'Artist' ? $request->type : "Client",
                        'email' => $request->email,
                    ]);

                    // Create new account record
                    $account = Account::create([
                        'personal_id' => $personalInformation->id,  // Assuming a relationship between the two tables
                        'fullname' => $request->first_name . ' ' . $request->last_name,
                        'type' => $request->role == 'Artist' ? $request->type : "Client",
                        'email' => $request->email,
                        'email_verified_at' => "-",
                        'password' => Hash::make($request->password),
                    ]);

                    // Handle Artist role registration if applicable
                    $certificateFilePath = null;
                    if ($request->role == 'Artist') {
                        $artist = Artist::create([
                            'personal_id' => $personalInformation->id,
                            'acc_id' => $account->id,
                            'full_name' => $request->first_name . ' ' . $request->last_name,
                            'price_range_max' => 00.00,
                            'price_range_min' => 00.00,
                            'occupation' => $request->occupation,
                        ]);

                        // Handle certificate file upload
                        if ($request->hasFile('certificate_file')) {
                            $certificateFile = $request->file('certificate_file');
                            $certificateFilePath = $certificateFile->store('certificates', 'public');
                        }

                        // Save file path to certificates table
                        if ($certificateFilePath) {
                            Certificate::create([
                                'acc_id' => $account->id,
                                'file_path' => $certificateFilePath,
                            ]);
                        }
                    }

                    if ($request->role == 'Client') {

                        // Handle certificate file upload
                        if ($request->hasFile('present_id')) {
                            $certificateFile = $request->file('present_id');
                            $certificateFilePath = $certificateFile->store('present_id', 'public');
                        }

                        // Save file path to certificates table
                        if ($certificateFilePath) {
                            ClientID::create([
                                'acc_id' => $account->id,
                                'file_path' => $certificateFilePath,
                                'id_type' => $request->id_type
                            ]);
                        }
                    }

                    DB::commit();

                    // Send email for registration
                    $fullname =  $request->first_name . ' ' . $request->last_name;
                    Mail::to($request->email)->send(new RegistrationEmail($fullname));

                    return response()->json([
                        'message' => 'You have successfully re-registered. Your account is under verification.',
                        'status' => 200,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'This email is already verified. Please Login your account',
                        'status' => 200,
                    ], 200);
                }
            } else {
                // If no account is found with the same email, proceed with registration
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
                    'type' => $request->role == 'Artist' ? $request->type : "Client",
                    'email' => $request->email,
                ]);

                // Create new account record
                $account = Account::create([
                    'personal_id' => $personalInformation->id,  // Assuming a relationship between the two tables
                    'fullname' => $request->first_name . ' ' . $request->last_name,
                    'type' => $request->role == 'Artist' ? $request->type : "Client",
                    'email' => $request->email,
                    'email_verified_at' => "-",
                    'password' => Hash::make($request->password),
                ]);

                // Handle Artist role registration if applicable
                $certificateFilePath = null;
                if ($request->role == 'Artist') {
                    $artist = Artist::create([
                        'personal_id' => $personalInformation->id,
                        'acc_id' => $account->id,
                        'full_name' => $request->first_name . ' ' . $request->last_name,
                        'price_range_max' => 00.00,
                        'price_range_min' => 00.00,
                        'occupation' => $request->occupation,
                    ]);

                    // Handle certificate file upload
                    if ($request->hasFile('certificate_file')) {
                        $certificateFile = $request->file('certificate_file');
                        $certificateFilePath = $certificateFile->store('certificates', 'public');
                    }

                    // Save file path to certificates table
                    if ($certificateFilePath) {
                        Certificate::create([
                            'acc_id' => $account->id,
                            'file_path' => $certificateFilePath,
                        ]);
                    }
                }

                if ($request->role == 'Client') {

                    // Handle certificate file upload
                    if ($request->hasFile('present_id')) {
                        $certificateFile = $request->file('present_id');
                        $certificateFilePath = $certificateFile->store('present_id', 'public');
                    }

                    // Save file path to certificates table
                    if ($certificateFilePath) {
                        ClientID::create([
                            'acc_id' => $account->id,
                            'file_path' => $certificateFilePath,
                            'id_type' => $request->id_type
                        ]);
                    }
                }

                DB::commit();

                // Send email for registration
                $fullname =  $request->first_name . ' ' . $request->last_name;
                Mail::to($request->email)->send(new RegistrationEmail($fullname));

                // Return success response
                return response()->json([
                    'message' => 'Your account has been successfully created. Please wait for the verification process. Also please check your email for updates.Thank you!',
                    'data' => [
                        'Info' => $personalInformation,
                        'account' => $account,
                        'certificate' => $certificateFilePath
                    ],
                    'status' => 201,
                ], 201);
            }
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
        $account = Account::where('email', '=', $request->email)->where('is_verify', true)->first();
        if ($account) {
            // Check if the password matches
            if (Hash::check($request->password, $account->password)) {
                if ($account->is_verify == 1) {
                    if ($account->is_disable == 1) {
                        return response()->json([
                            'message' => 'Your account has been disabled. Please contact your administrator. Thank you',
                            'status' => -4,
                            'token' => null,
                            'data' => $account
                        ]);
                    } else {
                        $token = $account->createToken($request->email)->plainTextToken;
                        $account->load(['personalInfo', 'profilePhoto']);
                        return response()->json([
                            'message' => 'Successful Logged In!',
                            'status' => 1,
                            'token' => $token,
                            'data' => $account
                        ]);
                    }
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

    public function pending_users(Request $request)
    {

        $accounts = Account::with(['personalInfo', 'certificate', 'valid_id']) // Eager load personalInfo relationship
            ->where('is_verify', false) // Filter by unverified users
            ->get();
        return response()->json([
            'message' => 'pending users',
            'data' => $accounts,
            'status' => 1
        ]);
    }

    public function verify(Request $request)
    {
        // Find the account by ID
        $account = Account::find($request->id);

        // Check if the account exists
        if (!$account) {
            return response()->json([
                'message' => 'Account not found',
                'status' => 0
            ], 404);
        }

        // Update the 'is_verify' status to true (1)
        $account->is_verify = 1;
        $account->save(); // Save the changes
        $fullname = $account->fullname;
        Mail::to($account->email)->send(new Verified($fullname));
        // Return success response
        return response()->json([
            'message' => 'User verified successfully',
            'status' => 1,
            'data' => $account // Optional: you can return the updated account data
        ]);
    }

    public function cancel_verify(Request $request)
    {
        $account = Account::where('email', $request->email)->first();

        // Check if the account exists
        if (!$account) {
            return response()->json([
                'message' => 'Account not found',
                'status' => 0
            ], 404);
        }

        // Update the 'is_cancel' status to true (1)
        $account->is_cancel = true;
        $account->save(); // Save the changes
        $fullname = $account->fullname;
        Mail::to($request->email)->send(new RejectEmail($request->reason, $fullname));
        // Return success response
        return response()->json([
            'message' => 'User verification cancelled successfully',
            'status' => 1,
            'data' => $account // Optional: you can return the updated account data
        ]);
    }

    public function registered_account(Request $request)
    {
        // Retrieve accounts that are verified and have a related PersonalInfo with role "Artist"
        // $accounts = Account::where('is_verify', true)
        //     ->whereHas('personalInfo', function ($query) {
        //         $query->where('role', 'Artist');
        //     })
        //     ->get();
        $accounts = Account::where('is_verify', true)->get();
        return response()->json([
            'message' => 'Registered artists',
            'data' => $accounts,
            'status' => 1,
        ]);
    }

    public function enable_or_disable_acc(Request $request)
    {
        // Find the account by ID
        $account = Account::find($request->id);

        // Check if the account exists
        if (!$account) {
            return response()->json([
                'message' => 'Account not found',
                'status' => 0
            ], 404);
        }

        // Update the 'delete' status to true (1)
        $account->is_disable = $request->is_disable;
        $account->save(); // Save the changes

        // Return success response
        return response()->json([
            'message' => 'User update successfully',
            'status' => 1,
            'data' => $account // Optional: you can return the updated account data
        ]);
    }

    public function change_password(Request $request)
    {
        // Find the user by email
        $user = Account::where('email', $request->email)->first();

        // If the user doesn't exist, return an error
        if (!$user) {
            return response()->json(['error' => 'Account not found.'], 404);
        }

        // Update the user's password in the database
        $user->password = Hash::make($request->new_password); // Hash the password for security
        $user->save();
        return response()->json(['message' => 'Password update successfully']);
    }
}
