<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\ImagePost;
use Illuminate\Support\Facades\DB;
use App\Models\VideoPost;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // public function create_post(Request $request)
    // {
    //     // Validate incoming request
    //     $validated = $request->validate([
    //         // 'acc_id' => 'required|integer', // Assuming acc_id is passed
    //         'description' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         // 'email' => 'required|email', // Assuming email is passed for file naming
    //     ]);

    //     try {
    //         // Begin transaction for data integrity
    //         DB::beginTransaction();

    //         // Create the post
    //         $post = Post::create([
    //             'acc_id' => $request->acc_id,
    //             'description' => $request->description ?? null,
    //             'like' => 0, // Default: not approved
    //         ]);

    //         // Handle image upload if present
    //         if ($request->hasFile('image')) {
    //             // Build the storage path
    //             $email = $request->email;
    //             $fileName = str_replace('@', '_', $email) . '.' . $request->file('image')->getClientOriginalExtension();
    //             $storagePath = "post_images/{$fileName}";

    //             // Store the file
    //             $request->file('image')->storeAs('public', $storagePath);

    //             // Build the full URL for the image
    //             $imageUrl = isset($storagePath)
    //                 ? url("storage/{$storagePath}")
    //                 : null;

    //             // Create the image record
    //             ImagePost::create([
    //                 'post_id' => $post->id,
    //                 'image_path' => $imageUrl,
    //             ]);
    //         }

    //         // Commit transaction
    //         DB::commit();



    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Post created successfully.',
    //             'post' => $post,
    //             'image_url' => $imageUrl, // Full image URL
    //         ]);
    //     } catch (\Exception $e) {
    //         // Rollback on failure
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create post. ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }
    public function create_post(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
            'video' => 'nullable|mimes:mp4,avi,mov,mkv|max:10240', // Video validation (max 10MB)
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
                // Build the storage path for image with a unique file name
                $email = $request->email;
                $extension = $request->file('image')->getClientOriginalExtension();
                $fileName = Str::uuid() . '.' . $extension; // Unique file name using UUID
                $storagePath = "post_images/{$fileName}";

                // Store the image file
                $request->file('image')->storeAs('public', $storagePath);

                // Build the full URL for the image
                $imageUrl = url("storage/{$storagePath}");

                // Create the image record
                ImagePost::create([
                    'post_id' => $post->id,
                    'image_path' => $imageUrl,
                ]);
            }

            // Handle video upload if present
            if ($request->hasFile('video')) {
                // Build the storage path for video with a unique file name
                $email = $request->email;
                $extension = $request->file('video')->getClientOriginalExtension();
                $fileName = Str::uuid() . '.' . $extension; // Unique file name using UUID
                $storagePath = "post_videos/{$fileName}";

                // Store the video file
                $request->file('video')->storeAs('public', $storagePath);

                // Build the full URL for the video
                $videoUrl = url("storage/{$storagePath}");

                // Create the video record in the post
                VideoPost::create([
                    'post_id' => $post->id,
                    'video_path' => $videoUrl,
                ]);
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully.',
                'post' => $post,
                'image_url' => isset($imageUrl) ? $imageUrl : null, // Return image URL if it exists
                'video_url' => isset($videoUrl) ? $videoUrl : null, // Return video URL if it exists
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
            ->with('videos')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    public function get_my_post(Request $request)
    {
        // Retrieve all verified posts with their related image posts
        $posts = Post::where('acc_id', $request->acc_id) // Only approved posts
            ->with('images') // Include related images
            ->with('videos')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    public function for_verification_post(Request $request)
    {
        // Retrieve all verified posts with their related image posts
        $posts = Post::with(['images', 'videos', 'UserInfo']) // Include related images
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

    public function display_post_by_search_artist(Request $request)
    {
        // Retrieve all verified posts with their related image posts
        $posts = Post::where('is_approved', 1)->where('acc_id', $request->acc_id) // Only approved posts
            ->with('images') // Include related images
            ->with('videos')
            ->with('ProfilePhoto')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    public function update_post(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max size 2MB
            'video' => 'nullable|mimes:mp4,avi,mov,mkv|max:10240', // Max size 10MB
        ]);

        try {
            DB::beginTransaction();

            // Find the post
            $post = Post::find($request->post_id);

            // Update description
            $post->description = $request->description;
            $post->save();

            // Handle image update
            if ($request->hasFile('image')) {
                $existingImage = $post->images()->first();
                if ($existingImage) {
                    // Delete old image if it exists
                    Storage::delete('public/' . $existingImage->image_path);
                    $existingImage->delete();
                }

                // Save new image
                $fileName = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
                $storagePath = "post_images/{$fileName}";
                $request->file('image')->storeAs('public', $storagePath);

                $post->images()->create([
                    'image_path' => url("storage/{$storagePath}"),
                ]);
            }

            // Handle video update
            if ($request->hasFile('video')) {
                $existingVideo = $post->videos()->first();
                if ($existingVideo) {
                    // Delete old video if it exists
                    Storage::delete('public/' . $existingVideo->video_path);
                    $existingVideo->delete();
                }

                // Save new video
                $fileName = Str::uuid() . '.' . $request->file('video')->getClientOriginalExtension();
                $storagePath = "post_videos/{$fileName}";
                $request->file('video')->storeAs('public', $storagePath);

                $post->videos()->create([
                    'video_path' => url("storage/{$storagePath}"),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully.',
                'updatedImages' => $post->images,
                'updatedVideos' => $post->videos,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update post. ' . $e->getMessage(),
            ], 500);
        }
    }
    public function delete_post(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'post_id' => 'required|integer|exists:posts,id', // Ensure post_id is valid and exists
        ]);

        try {
            // Begin transaction for data integrity
            DB::beginTransaction();

            // Find the post by ID
            $post = Post::find($request->post_id);

            // Check if the post exists
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            // // Delete associated images if they exist
            // $images = $post->images;
            // foreach ($images as $image) {
            //     Storage::delete('public/' . $image->image_path); // Delete the image file
            //     $image->delete(); // Delete the image record
            // }

            // // Delete associated videos if they exist
            // $videos = $post->videos;
            // foreach ($videos as $video) {
            //     Storage::delete('public/' . $video->video_path); // Delete the video file
            //     $video->delete(); // Delete the video record
            // }

            // Delete the post
            $post->delete();
            $video = ImagePost::find($request->post_id);
            $video->delete();
            $image = VideoPost::find($request->post_id);
            $image->delete();

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully.',
            ]);
        } catch (\Exception $e) {
            // Rollback on failure
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post. ' . $e->getMessage(),
            ], 500);
        }
    }
}
