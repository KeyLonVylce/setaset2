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
        $user = auth()->user();
    
        $query = \App\Models\Notification::query();
    
        // FILTER STATUS
        if (request('status') == 'read') {
            $query->whereJsonContains('read_by', $user->id);
        }
    
        if (request('status') == 'unread') {
            $query->where(function ($q) use ($user) {
                $q->whereNull('read_by')
                  ->orWhereJsonDoesntContain('read_by', $user->id);
            });
        }
    
        // FILTER TYPE
        if (request('type') && request('type') != 'all') {
            $query->where('type', request('type'));
        }
    
        // FILTER ROLE (punya kamu sebelumnya)
        $query->where(function ($q) use ($user) {
            $q->whereNull('target_role')
              ->orWhere('target_role', 'all')
              ->orWhere('target_role', $user->role);
        });
    
        $notifications = $query->latest()->paginate(10)->withQueryString();
    
        return view('notifications.index', compact('notifications', 'user'));
    }

    public function markAsRead($id)
    {
        $user = Auth::guard('stafaset')->user();

        $notification = Notification::findOrFail($id);
        $notification->markReadBy($user->id);

        return back();
    }

    public function realtime()
    {
        $user = Auth::guard('stafaset')->user();

        $unread = $this->baseQuery($user)
            ->get()
            ->filter(fn($n) => !$n->isReadBy($user->id))
            ->count();

        return response()->json([
            'unread' => $unread
        ]);
    }
}