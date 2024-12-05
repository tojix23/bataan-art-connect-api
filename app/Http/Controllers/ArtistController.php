<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller
{
    public function artist_list(Request $request)
    {
        // Eager load the 'profilePhoto' relationship
        $verifiedArtists = Artist::with('profilePhoto') // Load the profile photo relationship
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
                    'full_name' => $artist->full_name,
                    'occupation' => $artist->occupation,
                    'price_range' => $artist->price_range,
                    'image' => $artist->profilePhoto ? $artist->profilePhoto->image_path : null, // Access the image path from the related profile photo
                ];
            }),
            'status' => 1,
        ]);
    }
}
