<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $user = auth()->user();
    
        $query = \App\Models\Notification::query();
    
        // FILTER STATUS
        $status = $request->input('status');
        if ($status === 'read') {
            $query->whereJsonContains('read_by', $user->id);
        } elseif ($status === 'unread') {
            $query->where(function ($q) use ($user) {
                $q->whereNull('read_by')
                  ->orWhereJsonDoesntContain('read_by', $user->id);
            });
        }
    
        // FILTER TYPE
        $type = $request->input('type');
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }
    
        // FILTER TARGET ROLE
        $query->where(function ($q) use ($user) {
            $q->whereNull('target_role')
              ->orWhere('target_role', 'all')
              ->orWhere('target_role', $user->role);
        });
    
        // Ambil SEMUA notifikasi
        $notifications = $query->latest()->get()->map(function($notif) {
            $notif->created_at_human = $notif->created_at->diffForHumans();
            return $notif;
        });
    
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