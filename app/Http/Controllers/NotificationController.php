<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationRead;
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
            ->with(['reads' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications', 'user'));
    }


    public function markAsRead($id)
    {
        $user = Auth::guard('stafaset')->user();

        NotificationRead::updateOrCreate(
            [
                'notification_id' => $id,
                'user_id' => $user->id,
            ],
            [
                'read_at' => now(),
            ]
        );

        return back();
    }


    public function realtime()
    {
        $user = Auth::guard('stafaset')->user();
    
        $unread = $this->baseQuery($user)
            ->whereDoesntHave('reads', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereNotNull('read_at');
            })
            ->count();
    
        return response()->json([
            'unread' => $unread
        ]);
    }
}
