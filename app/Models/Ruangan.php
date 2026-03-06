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
        // 'no_urut' → DIHAPUS (redundan, dihitung saat query/tampil)
        'nama_barang',
        'merk_model',
        'no_seri_pabrik',
        'ukuran',
        'bahan',
        'tahun_pembuatan',
        'kode_barang',
        'jumlah',
        'harga_perolehan', // sekarang decimal(15,2)
        'kondisi',
        'keterangan',
    ];

    protected $casts = [
        'tahun_pembuatan'  => 'integer',
        'jumlah'           => 'integer',
        'harga_perolehan'  => 'decimal:2', // cast ke decimal — sebelumnya string
    ];

    /**
     * Relasi ke ruangan
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    // =============================================
    // ACCESSOR
    // =============================================

    /**
     * Total nilai barang (jumlah × harga)
     */
    public function getTotalNilaiAttribute(): float
    {
        return (float) $this->jumlah * (float) ($this->harga_perolehan ?? 0);
    }

    /**
     * Label kondisi yang bisa dibaca manusia
     */
    public function getKondisiLabelAttribute(): string
    {
        return match ($this->kondisi) {
            'B'  => 'Baik',
            'KB' => 'Kurang Baik',
            'RB' => 'Rusak Berat',
            default => '-',
        };
    }

    // Accessor backward-compat untuk view lama yang masih pakai ini
    public function getKeadaanBaikAttribute(): int
    {
        return $this->kondisi === 'B' ? $this->jumlah : 0;
    }

    public function getKeadaanKurangBaikAttribute(): int
    {
        return $this->kondisi === 'KB' ? $this->jumlah : 0;
    }

    public function getKeadaanRusakBeratAttribute(): int
    {
        return $this->kondisi === 'RB' ? $this->jumlah : 0;
    }
}