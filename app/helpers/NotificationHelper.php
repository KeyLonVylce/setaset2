<?php
namespace App\Helpers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationHelper
{
    public static function create(
        string $type,
        string $aksi,
        string $pesan,
        ?string $targetRole = null
    ) {
        Notification::create([
            'type'        => $type,
            'aksi'        => $aksi,
            'pesan'       => $pesan,
            'target_role' => $targetRole, // admin | staff | null
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);
    }
}
