<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\MessageReply;

class MessageController extends Controller
{
    public function send_a_message(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'acc_id' => 'required|integer',
            'sender_id' => 'required|integer',
            'reciever_id' => 'required|integer',
            'content' => 'required|string',

        ]);

        // Create a new message and insert it into the database
        $message = Message::create([
            'acc_id' => $validated['acc_id'],
            'sender_id' => $validated['sender_id'],
            'reciever_id' => $validated['reciever_id'],
            'content' => $validated['content'],
            'is_read' => $validated['is_read'] ?? false, // Default to false if not provided
        ]);

        // Return a response (could be success message or the created message)
        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }

    public function get_my_message(Request $request)
    {
        // Retrieve all verified posts with their related image posts
        // $message = Message::where('acc_id', $request->acc_id)
        //     ->with(['accountInfo', 'replies'])
        //     ->orderBy('created_at', 'desc')
        //     ->get();
        // Retrieve messages where the user is either the sender or the receiver
        $messages = Message::where('acc_id', $request->acc_id) // Sent messages
            ->orWhere('reciever_id', $request->acc_id) // Received messages
            ->with(['accountInfo', 'replies', 'sender', 'reciever'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function reply_a_message(Request $request)
    {
        $validated = $request->validate([
            'message_id' => 'required',
            'sender_id' => 'required',
            'content' => 'required|string|max:5000',
        ]);

        $reply = MessageReply::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully.',
            'data' => $reply,
        ]);
    }
}
