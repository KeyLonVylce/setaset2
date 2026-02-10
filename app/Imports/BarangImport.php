<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BarangImport implements ToModel, WithStartRow
{
    protected $ruangan_id;

    public function __construct($ruangan_id)
    {
        $this->ruangan_id = $ruangan_id;
    }

    public function startRow(): int
    {
        return 12;
    }

    public function model(array $row)
    {
        // Helper clean
        $clean = function ($value) {
            if (is_string($value)) {
                $v = trim($value);
                return ($v === '' || $v === '-' ? null : $v);
            }
            return $value;
        };

        // Barang pasti punya nama barang
        if (empty($clean($row[0]))) {
            return null;
        }

        // ==========================
        // Mapping kolom data barang (shifted by -1 karena no no_urut removed)
        // ==========================
        $nama_barang     = $clean($row[0]);
        $merk_model      = $clean($row[1]);
        $no_seri_pabrik  = $clean($row[2]);
        $ukuran          = $clean($row[3]);
        $bahan           = $clean($row[4]);
        $tahun           = $clean($row[5]);

        $kode_barang = implode('.', array_filter([
            $clean($row[6] ?? null),
            $clean($row[7] ?? null),
            $clean($row[8] ?? null),
            $clean($row[9] ?? null),
            $clean($row[10] ?? null),
            $clean($row[11] ?? null),
        ], fn($v) => $v !== null && $v !== '' && $v !== '-'));

        $jumlah = $clean($row[12]);
        $harga  = $clean($row[13]);

        // ==========================
        // Kondisi barang
        // ==========================
        $norm = fn($v) => ($v && trim($v) !== '-' ? strtolower(trim($v)) : null);

        $hasB  = $norm($row[14] ?? null);
        $hasKB = $norm($row[15] ?? null);
        $hasRB = $norm($row[16] ?? null);

        $kondisi = null;
        if ($hasRB) $kondisi = 'RB';
        elseif ($hasKB) $kondisi = 'KB';
        elseif ($hasB)  $kondisi = 'B';

        $keterangan = $clean($row[17] ?? null);

        // ==========================
        // Parsing angka
        // ==========================
        $jumlah_clean = (int) preg_replace('/[^0-9]/', '', ($jumlah ?? '0'));
        $harga_clean  = preg_replace('/[^0-9]/', '', ($harga ?? '0'));

        return new Barang([
            'ruangan_id'       => $this->ruangan_id,
            'nama_barang'      => $nama_barang,
            'merk_model'       => $merk_model,
            'no_seri_pabrik'   => $no_seri_pabrik,
            'ukuran'           => $ukuran,
            'bahan'            => $bahan,
            'tahun_pembuatan'  => is_numeric($tahun) ? (int) $tahun : null,
            'kode_barang'      => $kode_barang ?: null,
            'jumlah'           => $jumlah_clean,
            'harga_perolehan'  => $harga_clean ?: null,
            'kondisi'          => $kondisi ?: null,
            'keterangan'       => $keterangan ?: "-",
        ]);
    }
}