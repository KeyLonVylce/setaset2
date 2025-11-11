<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuInventaris extends Model
{
    use HasFactory;

    protected $table = 'kartu_inventaris';

    protected $fillable = [
        'ruangan_id',
        'tanggal',
        'mengetahui_id',
        'penanggung_jawab_id',
        'pengelola_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function mengetahui()
    {
        return $this->belongsTo(Pejabat::class, 'mengetahui_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(Pejabat::class, 'penanggung_jawab_id');
    }

    public function pengelola()
    {
        return $this->belongsTo(Pejabat::class, 'pengelola_id');
    }
}