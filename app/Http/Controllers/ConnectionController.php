<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Connection;
use App\Models\Notification;

class ConnectionController extends Controller
{
    public function send_connection(Request $request)
    {
        $request->validate([
            'connected_id' => 'required', // Validate that the connected user exists
        ]);

        $acc_id = $request->acc_id; // Get the currently authenticated user (User 1)
        $receiverId = $request->connected_id; // ID of User 2
        // Create a new connection request
        $connection = Connection::create([
            'acc_id' => $acc_id,
            'connected_id' => $receiverId,
            'status' => 'pending', // Initial status

        ]);

        $notification = Notification::create([
            'acc_id' => $acc_id,
            'notify_id' => $receiverId,
            'type_notif' => 'Connection', // Initial status
            'message' => 'Connection request sent', // Initial status
        ]);

        return response()->json([
            'message' => 'Connection request sent successfully.',
            'connection' => $connection,
        ], 201);
    }

    public function connection_status_client(Request $request)
    {
        // Retrieve connection
        $connection = Connection::where('acc_id', $request->acc_id)->with('UserInfo')  // Only approved posts
            ->get();

        return response()->json([
            'success' => true,
            'data' => $connection,
        ]);
    }

    public function connection_status_artist(Request $request)
    {
        // Retrieve connection
        $connection = Connection::where('connected_id', $request->acc_id)->with('UserInfoClient')  // Only approved posts
            ->get();

        return response()->json([
            'success' => true,
            'data' => $connection,
        ]);
    }

    public function connection_status_artist_and_client(Request $request)
    {
        // Retrieve connection
        $connection = Connection::where('acc_id', $request->acc_id)->where('connected_id', $request->connected_id) // Only approved posts
            ->get();

        return response()->json([
            'success' => true,
            'data' => $connection,
        ]);
    }

    public function approve_connection(Request $request)
    {
        $request->validate([
            'connection_id' => 'required', // Ensure user exists
        ]);

        // Find the connection by acc_id and connected_id
        $connection = Connection::where('id', $request->connection_id)
            ->where('status', 'pending') // Only approve pending connections
            ->first();
        // If the connection doesn't exist or isn't pending, return an error
        if (!$connection) {
            return response()->json([
                'success' => false,
                'message' => 'Connection request not found or already approved.',
            ], 404);
        }


        $notification = Notification::create([
            'acc_id' => $request->current_user,
            'notify_id' => $request->notify_id,
            'type_notif' => 'Connection', // Initial status
            'message' => $request->artist_name . ' approved your request in connection', // Initial status
        ]);

        // Update the status to 'approved'
        $connection->status = 'accepted';
        $connection->save();

        return response()->json([
            'success' => true,
            'message' => 'Connection approved successfully.',
            'connection' => $connection,
        ]);
    }

    public function reject_connection(Request $request)
    {
        $request->validate([
            'connection_id' => 'required', // Ensure user exists
        ]);

        // Find the connection by acc_id and connected_id
        $connection = Connection::where('id', $request->connection_id)
            ->where('status', 'pending') // Only approve pending connections
            ->first();
        // If the connection doesn't exist or isn't pending, return an error
        if (!$connection) {
            return response()->json([
                'success' => false,
                'message' => 'Connection request not found or already approved.',
            ], 404);
        }

        $notification = Notification::create([
            'acc_id' => $request->current_user,
            'notify_id' => $request->notify_id,
            'type_notif' => 'Connection', // Initial status
            'message' => $request->artist_name . ' blocked your request in connection', // Initial status
        ]);
        // Update the status to 'approved'
        $connection->status = 'blocked';
        $connection->save();

        return response()->json([
            'success' => true,
            'message' => 'Connection blocked successfully.',
            'connection' => $connection,
        ]);
    }
}
