<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lantai extends Model
{
    use HasFactory;

    protected $table = 'lantais';

    protected $fillable = [
        'nama_lantai',
        'urutan',
        'keterangan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function ruangans()
    {
        return $this->hasMany(Ruangan::class, 'lantai_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('nama_lantai');
    }

    public function getTotalRuanganAttribute()
    {
        return $this->ruangans()->count();
    }

    public function getTotalBarangAttribute()
    {
        return $this->ruangans()
            ->withCount('barangs')
            ->get()
            ->sum('barangs_count');
    }
}