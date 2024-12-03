<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ProfilePhoto;

class ProfilePhotoController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Validate the uploaded file
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            // Generate the filename based on acc_id
            $fileName = $request->email . '.' . $request->file('image')->getClientOriginalExtension();
            $dateTime = now()->format('Y-m-d-H-i'); // Get current date and time as 'Y-m-d-H-i'
            // Check if the file already exists in storage
            // Check if a file with the same email name exists with any image extension
            $existingFilePathPng = storage_path('app/public/profile_photos/' . $fileName . '.png');
            $existingFilePathJpg = storage_path('app/public/profile_photos/' . $fileName . '.jpg');

            // Check and delete if the file exists
            if (file_exists($existingFilePathPng)) {
                unlink($existingFilePathPng); // Delete PNG
            } elseif (file_exists($existingFilePathJpg)) {
                unlink($existingFilePathJpg); // Delete JPG
            }

            // Store the image with the new file name
            $path = $request->file('image')->storeAs(
                'profile_photos',
                $fileName,
                'public'
            );

            // Save the new image path and the authenticated user's ID to the database
            $profilePhoto = ProfilePhoto::updateOrCreate(
                ['acc_id' => $request->acc_id], // Check if acc_id exists
                ['image_path' => asset('storage/' . $path)] // Update the image_path or insert new record
            );

            return response()->json([
                'message' => 'Profile photo uploaded successfully.',
                'data' => $profilePhoto,
                'image_path' => asset('storage/' . $path), // This gives the correct public URL
            ], 201);
        }

        return response()->json([
            'message' => 'No file uploaded.',
        ], 400);
    }
}
