<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'aksi',
        'pesan',
        'target_role',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function reads()
    {
        return $this->hasMany(NotificationRead::class);
    }

    public function isReadBy($userId)
    {
        return $this->reads()
            ->where('user_id', $userId)
            ->whereNotNull('read_at')
            ->exists();
    }
}




