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
        'can_edit',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'can_edit' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }
}