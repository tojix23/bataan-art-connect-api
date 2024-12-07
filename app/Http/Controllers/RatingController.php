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

        ]);


        // Create a new task using the create method
        Task::create([
            'creator_acc_id' => $validated['creator_acc_id'],
            'assignee_acc_id' => $validated['assignee_acc_id'],
            'assignee_name' => $validated['assignee_name'],
            'creator_name' => $validated['creator_name'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
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
}
