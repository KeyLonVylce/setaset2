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
        'lantai',
        'penanggung_jawab',
        'nip_penanggung_jawab',
        'keterangan',
    ];

    public function lantaiRelation()
    {
        return $this->belongsTo(Lantai::class, 'lantai_id');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'ruangan_id');
    }

    public function kartuInventaris()
    {
        return $this->hasMany(KartuInventaris::class, 'ruangan_id');
    }

    public function getNamaLantaiAttribute()
    {
        if ($this->lantaiRelation) {
            return $this->lantaiRelation->nama_lantai;
        }
        return $this->lantai;
    }

    public function getTotalBarangAttribute()
    {
        return $this->barangs()->sum('jumlah');
    }
}