<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class StafAset extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'stafaset';

    protected $fillable = [
        'username',
        'nama',
        'nip',
        'password',
        'role',
        'can_edit',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'can_edit' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function canEdit(): bool
    {
        return $this->can_edit === true;
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'staff' => 'Staff',
            default => 'Unknown'
        };
    }
}