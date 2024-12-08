<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

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
            'creator_acc_id' => $validated['creator_acc_id'],
            'assignee_acc_id' => $validated['assignee_acc_id'],
            'assignee_name' => $validated['assignee_name'],
            'creator_name' => $validated['creator_name'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'status' => 'pending'
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
}
