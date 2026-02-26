<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'ruangan_id',
        'nama_barang',
        'merk_model',
        'no_seri_pabrik',
        'ukuran',
        'bahan',
        'tahun_pembuatan',
        'kode_barang',
        'jumlah',
        'harga_perolehan',
        'kondisi',
        'keterangan',
    ];

    protected $casts = [
        'tahun_pembuatan' => 'integer',
        'jumlah' => 'integer',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function getTotalNilaiAttribute()
    {
        $harga = is_numeric($this->harga_perolehan) ? floatval($this->harga_perolehan) : 0;
        return $this->jumlah * $harga;
    }

    // Get kondisi counts for display (untuk backward compatibility)
    public function getKeadaanBaikAttribute()
    {
        return $this->kondisi === 'B' ? $this->jumlah : 0;
    }

    public function getKeadaanKurangBaikAttribute()
    {
        return $this->kondisi === 'KB' ? $this->jumlah : 0;
    }

    public function getKeadaanRusakBeratAttribute()
    {
        return $this->kondisi === 'RB' ? $this->jumlah : 0;
    }

    public function getKondisiLabelAttribute()
    {
        $labels = [
            'B' => 'Baik',
            'KB' => 'Kurang Baik',
            'RB' => 'Rusak Berat'
        ];
        return $labels[$this->kondisi] ?? '-';
    }
}