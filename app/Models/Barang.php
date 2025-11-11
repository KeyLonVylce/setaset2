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
        'no_urut',
        'nama_barang',
        'merk_model',
        'no_seri_pabrik',
        'ukuran',
        'bahan',
        'tahun_pembuatan',
        'kode_barang',
        'jumlah',
        'harga_perolehan',
        'keadaan_baik',
        'keadaan_kurang_baik',
        'keadaan_rusak_berat',
        'keterangan',
    ];

    protected $casts = [
        'harga_perolehan' => 'decimal:2',
        'tahun_pembuatan' => 'integer',
        'jumlah' => 'integer',
        'keadaan_baik' => 'integer',
        'keadaan_kurang_baik' => 'integer',
        'keadaan_rusak_berat' => 'integer',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function getTotalNilaiAttribute()
    {
        return $this->jumlah * $this->harga_perolehan;
    }
}