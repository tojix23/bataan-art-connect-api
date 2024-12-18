<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Rating;
use App\Models\Notification;
use App\Models\RatingAttachment;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function send_task(Request $request)
    {
        // Validate the incoming request (optional)
        $validated = $request->validate([
            'creator_acc_id' => 'required',
            'assignee_acc_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignee_name' => 'required|string|max:255',  // Add validation for assignee_name
            'creator_name' => 'required|string|max:255',  // Add validation for creator_name
            'start_date' => 'required'

        ]);


        // Create a new task using the create method
        Task::create([
            'package_type' => $request->package_type,
            'creator_acc_id' => $validated['creator_acc_id'],
            'assignee_acc_id' => $validated['assignee_acc_id'],
            'assignee_name' => $validated['assignee_name'],
            'creator_name' => $validated['creator_name'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'status' => 'pending'
        ]);

        $notification = Notification::create([
            'acc_id' => $validated['creator_acc_id'],
            'notify_id' => $validated['assignee_acc_id'],
            'type_notif' => 'Engagement', // Initial status
            'message' => $validated['creator_name'] . ' Send you a task', // Initial status
        ]);

        return response()->json([
            'message' => 'Task created successfully!',
        ], 201);
    }

    public function get_task_by_client(Request $request)
    {
        // Retrieve a specific task by ID
        $task = Task::where('creator_acc_id', $request->acc_id)->get();

        // Check if the task exists
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        return response()->json([
            'data' => $task
        ]);
    }


    public function get_task_by_artist(Request $request)
    {
        // Retrieve a specific task by ID
        $task = Task::where('assignee_acc_id', $request->acc_id)->get();

        // Check if the task exists
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        return response()->json([
            'data' => $task
        ]);
    }

    public function cancel_task_by_id(Request $request)
    {
        // Validate the request to ensure a task ID is provided
        $validated = $request->validate([
            'task_id' => 'required', // Ensure the task exists in the database
        ]);

        // Find the task by ID
        $task = Task::find($validated['task_id']);

        // Check if the task is already cancelled
        if ($task->status === 'Cancelled') {
            return response()->json([
                'message' => 'Task is already cancelled.',
            ], 400); // Return a bad request status
        }

        // Update the task status to 'Cancelled'
        $task->update(['status' => 'Cancelled']);

        // Return a success response
        return response()->json([
            'message' => 'Task successfully cancelled.',
            'task' => $task,
        ], 200); // HTTP 200 OK
    }

    public function get_task_by_artist_for_confirmation(Request $request)
    {
        // Retrieve a specific task by ID
        $task = Task::where('assignee_acc_id', $request->acc_id)->where('confirm_by_assignee', false)->get();

        // Check if the task exists
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        return response()->json([
            'data' => $task
        ]);
    }
    public function get_task_by_artist_confirmed(Request $request)
    {
        // Retrieve a specific task by ID
        $task = Task::where('assignee_acc_id', $request->acc_id)->where('confirm_by_assignee', true)->get();

        // Check if the task exists
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        return response()->json([
            'data' => $task
        ]);
    }

    public function confirm_task_by_artist(Request $request)
    {
        // Validate the request to ensure a task ID is provided
        $validated = $request->validate([
            'task_id' => 'required', // Ensure the task exists in the database
        ]);

        // Find the task by ID
        $task = Task::find($validated['task_id']);

        // Check if the task is already cancelled
        if ($task->status === 'Cancelled') {
            return response()->json([
                'message' => 'Task is already cancelled.',
            ], 400); // Return a bad request status
        }


        // Update the task status to 'Cancelled'
        $task->update(['confirm_by_assignee' => true]);

        $notification = Notification::create([
            'acc_id' => $request->current_user,
            'notify_id' =>  $request->notify_id,
            'type_notif' => 'Engagement', // Initial status
            'message' => $request->current_user_name . ' Accepted your task', // Initial status
        ]);


        // Return a success response
        return response()->json([
            'message' => 'Task successfully accept.',
            'task' => $task,
        ], 200); // HTTP 200 OK
    }
    public function mark_as_done_by_artist(Request $request)
    {
        // Validate the request to ensure a task ID is provided
        $validated = $request->validate([
            'task_id' => 'required', // Ensure the task exists in the database
        ]);

        // Find the task by ID
        $task = Task::find($validated['task_id']);

        // Check if the task is already cancelled
        if ($task->status === 'Cancelled') {
            return response()->json([
                'message' => 'Task is already cancelled.',
            ], 400); // Return a bad request status
        }

        // Update the task status to 'Cancelled'
        $task->update(['status' => 'completed']);

        $notification = Notification::create([
            'acc_id' => $request->current_user,
            'notify_id' =>  $request->notify_id,
            'type_notif' => 'Engagement', // Initial status
            'message' => $request->current_user_name . ' Completed the task', // Initial status
        ]);
        // Return a success response
        return response()->json([
            'message' => 'Task successfully completed.',
            'task' => $task,
        ], 200); // HTTP 200 OK
    }
    // public function rate_task(Request $request)
    // {
    //     // Validate the incoming request data
    //     $validatedData = $request->validate([
    //         'acc_id' => 'required', // Ensure acc_id refers to a valid user
    //         'rated_by' => 'required', // Ensure rated_by refers to a valid user
    //         'rated_for' => 'required', // Ensure rated_for refers to a valid user
    //         'rating_value' => 'required|integer|min:1|max:5', // Rating value must be between 1 and 5
    //         'comment' => 'nullable|string|max:500', // Optional comment
    //     ]);

    //     try {
    //         // Create a new rating entry
    //         $rating = Rating::create($validatedData);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Rating submitted successfully.',
    //             'data' => $rating,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to submit the rating. Please try again.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    public function rate_task(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'acc_id' => 'required', // Ensure acc_id refers to a valid user
            'rated_by' => 'required', // Ensure rated_by refers to a valid user
            'rated_for' => 'required', // Ensure rated_for refers to a valid user
            'rating_value' => 'required|integer|min:1|max:5', // Rating value must be between 1 and 5
            'comment' => 'nullable|string|max:500', // Optional comment
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional image upload
        ]);

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Create a new rating entry
            $rating = Rating::create([
                'acc_id' => $validatedData['acc_id'],
                'rated_by' => $validatedData['rated_by'],
                'rated_for' => $validatedData['rated_for'],
                'rating_value' => $validatedData['rating_value'],
                'comment' => $validatedData['comment'] ?? null,
            ]);

            // Handle the image upload if provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filePath = $image->store('uploads/ratings', 'public'); // Store in public/uploads/ratings directory

                // Create a new entry in the RatingAttachment model
                RatingAttachment::create([
                    'rating_id' => $rating->id,
                    'rate_by' => $validatedData['rated_by'],
                    'file_path' => $filePath,
                ]);
            }

            $notification = Notification::create([
                'acc_id' => $validatedData['rated_by'],
                'notify_id' =>  $validatedData['acc_id'],
                'type_notif' => 'Rating', // Initial status
                'message' => $request->current_user_name . ' rated your task', // Initial status
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully.',
                'data' => $rating,
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit the rating. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_user_rating($userId)
    {
        $averageRating = Rating::getAverageRating($userId);

        return response()->json([
            'success' => true,
            'average_rating' => $averageRating,
        ]);
    }

    public function get_feedback(Request $request)
    {
        // Retrieve a specific task by ID
        $rate = Rating::where('rated_for', $request->task_id)->with('attachment')->get();

        // Check if the task exists
        if (!$rate) {
            return response()->json([
                'message' => 'ratings not found',
            ], 404);
        }

        return response()->json([
            'data' => $rate
        ]);
    }
}
