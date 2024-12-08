<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalInfo;

class PersonalInfoController extends Controller
{
    public function update_bio(Request $request)
    {
        $personal = PersonalInfo::where('id', $request->personal_id)->first();

        // Check if the account exists
        if (!$personal) {
            return response()->json([
                'message' => 'Account not found',
                'status' => 0
            ], 404);
        }

        // Update the 'is_cancel' status to true (1)
        $personal->bio = $request->bio;
        $personal->save(); // Save the changes

        // Return success response
        return response()->json([
            'message' => 'User update personal successfully',
            'status' => 1,
            'data' => $personal // Optional: you can return the updated account data
        ]);
    }
}
