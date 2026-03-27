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
        'read_by',
    ];

    protected $casts = [
        'read_by' => 'array',
    ];

    /**
     * Cek apakah notifikasi sudah dibaca oleh user tertentu.
     */
    public function isReadBy(int $userId): bool
    {
        $readBy = $this->read_by ?? [];
        return in_array($userId, $readBy);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca oleh user tertentu.
     */
    public function markReadBy(int $userId): void
    {
        $readBy = $this->read_by ?? [];

        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->update(['read_by' => $readBy]);
        }
    }
}