<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Connection;

class ConnectionController extends Controller
{
    public function send_connection(Request $request)
    {
        $request->validate([
            'connected_id' => 'required', // Validate that the connected user exists
        ]);

        $acc_id = $request->acc_id; // Get the currently authenticated user (User 1)
        $receiverId = $request->connected_id; // ID of User 2

        // // Check if a connection already exists
        // $existingConnection = Connection::where(function ($query) use ($acc_id, $receiverId) {
        //     $query->where('acc_id', $acc_id)
        //           ->where('connected_id', $receiverId);
        // })->orWhere(function ($query) use ($senderId, $receiverId) {
        //     $query->where('acc_id', $receiverId)
        //           ->where('connected_id', $senderId);
        // })->first();

        // if ($existingConnection) {
        //     return response()->json([
        //         'message' => 'A connection already exists or is pending.',
        //     ], 400);
        // }

        // Create a new connection request
        $connection = Connection::create([
            'acc_id' => $acc_id,
            'connected_id' => $receiverId,
            'status' => 'pending', // Initial status

        ]);

        return response()->json([
            'message' => 'Connection request sent successfully.',
            'connection' => $connection,
        ], 201);
    }
}
