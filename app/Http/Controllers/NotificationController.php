<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * AJAX: Get paginated notifications for logged-in user.
     */
    public function index()
    {
        $userId = Auth::id();
        $unreadCount = Notification::forUser($userId)->unread()->count();
        $notifications = Notification::forUser($userId)
            ->with('complaint')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * AJAX: Get unread count.
     */
    public function unreadCount()
    {
        $count = Notification::forUser(Auth::id())->unread()->count();
        return response()->json(['unread_count' => $count]);
    }

    /**
     * AJAX: Mark a notification as read.
     */
    public function markRead(int $id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Mark all notifications as read.
     */
    public function markAllRead()
    {
        Notification::forUser(Auth::id())->unread()->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Delete a single notification.
     */
    public function destroy(int $id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Delete all notifications.
     */
    public function destroyAll()
    {
        Notification::forUser(Auth::id())->delete();
        return response()->json(['success' => true]);
    }
}
