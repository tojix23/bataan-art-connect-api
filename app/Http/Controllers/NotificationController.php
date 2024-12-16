<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function get_unread_notif_conn(Request $request)
    {
        $unread =  Notification::where('notify_id', $request->acc_id)
            ->where('is_read', false)
            ->count();
        return response()->json(['data' => $unread]);
    }

    public function mark_as_read_conn_notif(Request $request)
    {
        $userId = $request->user()->id;

        Notification::where('notify_id', $request->acc_id)
            ->where('is_read', false)
            ->where('type_notif', 'Connection')
            ->update(['is_read' => true]);

        return response()->json(['status' => 'Notifications marked as read']);
    }
}
