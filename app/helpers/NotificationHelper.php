<?php

namespace App\Helpers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationHelper
{
    public static function create($type, $aksi, $pesan)
    {
        Notification::create([
            'type' => $type,
            'aksi' => $aksi,
            'pesan' => $pesan,
            'user_id' => Auth::id(),
        ]);
    }
}
