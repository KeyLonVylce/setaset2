<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'lantai_id',
        'nama_ruangan',
        'penanggung_jawab_id',
        'keterangan',
    ];

    public function lantai()
    {
        return $this->belongsTo(Lantai::class, 'lantai_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(Pejabat::class, 'penanggung_jawab_id');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'ruangan_id');
    }
}