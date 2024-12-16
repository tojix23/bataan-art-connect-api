<?php

namespace App\Http\Controllers;

use App\Models\LikePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikePostController extends Controller
{
    public function like_post(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|integer',
            'acc_id' => 'required|integer',
        ]);

        // Check if the user already liked/disliked the post
        $existing = DB::table('like_posts')
            ->where('post_id', $validated['post_id'])
            ->where('acc_id', $validated['acc_id'])
            ->first();

        if ($existing) {
            // Update action
            DB::table('like_posts')
                ->where('id', $existing->id)
                ->update([
                    'action' => $request->action,
                    'updated_at' => now(),
                ]);
        } else {
            // Insert new record
            DB::table('like_posts')->insert([
                'post_id' => $validated['post_id'],
                'acc_id' => $validated['acc_id'],
                'action' => $request->action,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Action recorded successfully!',
        ]);
    }
}
