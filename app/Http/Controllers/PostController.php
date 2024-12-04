<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\ImagePost;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function create_post(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            // 'acc_id' => 'required|integer', // Assuming acc_id is passed
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'email' => 'required|email', // Assuming email is passed for file naming
        ]);

        try {
            // Begin transaction for data integrity
            DB::beginTransaction();

            // Create the post
            $post = Post::create([
                'acc_id' => $request->acc_id,
                'description' => $request->description ?? null,
                'like' => 0, // Default: not approved
            ]);

            // Handle image upload if present
            if ($request->hasFile('image')) {
                // Build the storage path
                $email = $request->email;
                $fileName = str_replace('@', '_', $email) . '.' . $request->file('image')->getClientOriginalExtension();
                $storagePath = "post_images/{$fileName}";

                // Store the file
                $request->file('image')->storeAs('public', $storagePath);

                // Build the full URL for the image
                $imageUrl = isset($storagePath)
                    ? url("storage/{$storagePath}")
                    : null;

                // Create the image record
                ImagePost::create([
                    'post_id' => $post->id,
                    'image_path' => $imageUrl,
                ]);
            }

            // Commit transaction
            DB::commit();



            return response()->json([
                'success' => true,
                'message' => 'Post created successfully.',
                'post' => $post,
                'image_url' => $imageUrl, // Full image URL
            ]);
        } catch (\Exception $e) {
            // Rollback on failure
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create post. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verified_post(Request $request)
    {
        // Retrieve all verified posts with their related image posts
        $posts = Post::where('is_approved', 1)->where('acc_id', $request->acc_id) // Only approved posts
            ->with('images') // Include related images
            ->get();

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    public function for_verification_post(Request $request)
    {
        // Retrieve all verified posts with their related image posts
        $posts = Post::with(['images', 'UserInfo']) // Include related images
            ->get();

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    public function approve_post(Request $request)
    {
        // Find the account by ID
        $post = Post::find($request->post_id);

        // Check if the account exists
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 0
            ], 404);
        }

        // Update the 'is_verify' status to true (1)
        $post->is_approved = true;
        $post->save(); // Save the changes

        // Return success response
        return response()->json([
            'message' => 'Post verified successfully',
            'status' => 1,
            'data' => $post // Optional: you can return the updated account data
        ]);
    }

    public function cancel_post(Request $request)
    {
        // Find the account by ID
        $post = Post::find($request->post_id);

        // Check if the account exists
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 0
            ], 404);
        }

        // Update the 'is_verify' status to true (1)
        $post->delete = true;
        $post->save(); // Save the changes

        // Return success response
        return response()->json([
            'message' => 'Post cancel successfully',
            'status' => 1,
            'data' => $post // Optional: you can return the updated account data
        ]);
    }
}
