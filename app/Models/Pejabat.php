<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
    use HasFactory;

    protected $table = 'pejabats';

    protected $fillable = [
        'nama',
        'jabatan',
        'nip',
        'keterangan',
    ];

    /**
     * Pejabat bisa menjadi penanggung jawab di banyak ruangan
     */
    public function ruangans()
    {
        return $this->hasMany(Ruangan::class, 'penanggung_jawab_id');
    }
}