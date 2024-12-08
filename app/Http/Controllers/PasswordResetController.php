<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Account;

class PasswordResetController extends Controller
{
    public function sendPasswordResetEmail(Request $request)
    {
        // Find the user by email
        $user = Account::where('email', $request->email)->first();

        // If the user doesn't exist, return an error
        if (!$user) {
            return response()->json(['error' => 'Account not found.'], 404);
        }

        // Generate a new password (you can change the length if necessary)
        $newPassword = Str::random(10); // 10-character random password

        // Update the user's password in the database
        $user->password = bcrypt($newPassword);
        $user->save();

        // Generate the password reset link (optional if you want to keep the flow)
        $resetToken = Str::random(60); // This could be used to send a reset link, if needed
        $resetLink = route('password.reset', ['token' => $resetToken]);

        // Send the new password to the user's email
        Mail::to($user->email)->send(new ForgotPassword($newPassword));

        // Return a success message
        return response()->json(['message' => 'Password reset email sent!', 'new_password' => $newPassword]);
    }
}
