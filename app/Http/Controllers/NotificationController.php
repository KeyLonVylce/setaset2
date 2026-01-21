<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private function baseQuery($user)
    {
        return Notification::where(function ($q) use ($user) {
            $q->whereNull('target_role')
              ->orWhere('target_role', 'all')
              ->orWhere('target_role', $user->role);
        });
    }

    public function index()
    {
        $user = Auth::guard('stafaset')->user();

        $notifications = $this->baseQuery($user)
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        Notification::where('id', $id)->update([
            'read_at' => now()
        ]);

        return back();
    }

    public function realtime()
    {
        $user = Auth::guard('stafaset')->user();

        $unread = $this->baseQuery($user)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread' => $unread
        ]);
    }
}
