<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PindahBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'ruangan_asal',
        'ruangan_tujuan',
        'jumlah_pindah',
        'notes',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function asal()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_asal');
    }

    public function tujuan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_tujuan');
    }
}