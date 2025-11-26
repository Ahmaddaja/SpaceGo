<?php
namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = UserNotification::latest()->get();
        
        // Group by category
        $groupedNotifications = $notifications->groupBy('category');
        
        return view('notifications.index', compact('groupedNotifications'));
    }

    public function markAsRead($id)
    {
        $notification = UserNotification::findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        UserNotification::where('is_read', false)->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = UserNotification::findOrFail($id);
        $notification->delete();
        
        return response()->json(['success' => true]);
    }

    public function clearByCategory($category)
    {
        UserNotification::where('category', $category)->delete();
        
        return response()->json(['success' => true]);
    }

    public function getNotifications()
    {
        $notifications = UserNotification::latest()->take(10)->get();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count()
        ]);
    }
}