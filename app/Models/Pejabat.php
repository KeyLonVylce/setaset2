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
     * Pejabat bisa menjadi "mengetahui" di banyak kartu inventaris
     */
    public function kartuSebagaiMengetahui()
    {
        return $this->hasMany(KartuInventaris::class, 'mengetahui_id');
    }

    /**
     * Pejabat bisa menjadi penanggung jawab di banyak kartu inventaris
     */
    public function kartuSebagaiPenanggungJawab()
    {
        return $this->hasMany(KartuInventaris::class, 'penanggung_jawab_id');
    }

    /**
     * Pejabat bisa menjadi pengelola di banyak kartu inventaris
     */
    public function kartuSebagaiPengelola()
    {
        return $this->hasMany(KartuInventaris::class, 'pengelola_id');
    }
}