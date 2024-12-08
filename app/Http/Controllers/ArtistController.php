<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller
{
    public function artist_list(Request $request)
    {
        // Eager load the 'profilePhoto' relationship
        $verifiedArtists = Artist::with(['profilePhoto', 'personalInfo', 'ratings']) // Load the profile photo relationship
            ->whereHas('account', function ($query) {
                $query->where('is_verify', true); // Filter verified artists
            })
            ->where('occupation', '=', $request->category) // Filter by category
            ->get();

        // Map over the artists and include the profile photo image path
        return response()->json([
            'message' => 'Registered artists',
            'data' => $verifiedArtists->map(function ($artist) {
                return [
                    'id' => $artist->id,
                    'personal_id' => $artist->personal_id,
                    'acc_id' => $artist->acc_id,
                    'full_name' => $artist->full_name,
                    'occupation' => $artist->occupation,
                    'price_range_max' => $artist->price_range_max,
                    'price_range_min' =>  $artist->price_range_min,
                    'image' => $artist->profilePhoto ? $artist->profilePhoto->image_path : null, // Access the image path from the related profile photo
                    'average_rating' => $artist->average_rating, // Add average rating here
                    'ratings' => $artist->ratings->pluck('rating_value'), // Only include the rating values
                ];
            }),
            'status' => 1,
        ]);
    }

    public function update_service_rate(Request $request)
    {
        // Find the account by ID
        $update = Artist::where('acc_id', $request->acc_id)->first();

        // Check if the account exists
        if (!$update) {
            return response()->json([
                'message' => 'Artist not found',
                'status' => 0
            ], 404);
        }

        // Update the 'is_verify' status to true (1)
        $update->price_range_max = $request->price_range_max;
        $update->price_range_min = $request->price_range_min;
        $update->save(); // Save the changes

        // Return success response
        return response()->json([
            'message' => 'Service rate successfully',
            'status' => 1,
            'data' => $update // Optional: you can return the updated account data
        ]);
    }
    public function get_rate(Request $request)
    {
        // Find the account by ID
        $get = Artist::where('acc_id', $request->acc_id)->select('id', 'acc_id', 'price_range_max', 'price_range_min')->first();

        // Check if the account exists
        if (!$get) {
            return response()->json([
                'message' => 'Artist not found',
                'status' => 0
            ], 404);
        }


        // Return success response
        return response()->json([
            'message' => 'get rate successfully',
            'status' => 1,
            'data' => $get // Optional: you can return the updated account data
        ]);
    }

    public function get_artist_by_id(Request $request)
    {
        $artist = Artist::with(['personalInfo', 'certificate', 'profilePhoto']) // Eager load personalInfo relationship
            ->where('acc_id', $request->acc_id) // Filter by unverified users
            ->get();
        return response()->json([
            'message' => 'Artist',
            'data' => $artist,
            'status' => 1
        ]);
    }
}
