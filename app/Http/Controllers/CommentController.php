<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function add_comment(Request $request)
    {
        // Create a Validator instance
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id', // Ensure the post exists
            'comment' => 'required|string|max:255', // Limit comment length
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create a new comment
            $comment = Comment::create([
                'post_id' => $request->post_id,
                'acc_id' => $request->acc_id,
                'comment' => $request->comment,
                'is_remove' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully.',
                'data' => $comment,
            ], 201);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $accounts = Comment::with(['UserInfo', 'profilePhoto'])->where('post_id', $request->post_id) // Filter by unverified users
            ->get();
        return response()->json([
            'message' => 'list comments',
            'data' => $accounts,
            'status' => 1
        ]);
    }
}
