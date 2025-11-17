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
        return 12; // baris pertama data barang
    }

    public function model(array $row)
    {
        // Helper clean value
        $clean = function ($value) {
            if (is_string($value)) {
                $v = trim($value);
                return ($v === '' || $v === '-' ? null : $v);
            }
            return $value;
        };

        // ==== Abaikan baris footer / tanda tangan / pejabat ==== //
        $footerKeywords = [
            'mengetahui',
            'kepala bagian',
            'tata usaha',
            'pengelola barang',
            'penanggungjawab',
            'bandung',
            'nip',
            'hj.',
            'astria',
            'handi',
            'nandang'
        ];

        $rowString = strtolower(trim(implode(' ', $row)));

        foreach ($footerKeywords as $kw) {
            if (str_contains($rowString, $kw)) {
                return null; // baris footer → skip
            }
        }

        // Jika kolom nama barang kosong → skip
        if (empty($clean($row[1]))) {
            return null;
        }

        // ==========================
        //  MAPPING KOLOM EXCEL
        // ==========================
        $no_urut         = $clean($row[0]);
        $nama_barang     = $clean($row[1]);
        $merk_model      = $clean($row[3]);
        $no_seri_pabrik  = $clean($row[4]);
        $ukuran          = $clean($row[5]);
        $bahan           = $clean($row[6]);
        $tahun           = $clean($row[7]);

        // Build kode barang from columns 9-14
        $kode_barang = implode('.', array_filter([
            $clean($row[9] ?? null),
            $clean($row[10] ?? null),
            $clean($row[11] ?? null),
            $clean($row[12] ?? null),
            $clean($row[13] ?? null),
            $clean($row[14] ?? null),
        ], fn($v) => $v !== null && $v !== '' && $v !== '-'));

        $jumlah = $clean($row[15]);
        $harga  = $clean($row[16]);

        // ===== PERBAIKAN KONDISI ===== //
        $kondisi = null; // default

        $normalize = function ($v) {
            if (!$v) return null;
            $v = trim(strtolower($v));
            return ($v === '' || $v === '-' ? null : $v);
        };

        $hasB  = $normalize($row[17] ?? null);
        $hasKB = $normalize($row[18] ?? null);
        $hasRB = $normalize($row[19] ?? null);

        if ($hasRB) {
            $kondisi = 'RB';
        } elseif ($hasKB) {
            $kondisi = 'KB';
        } elseif ($hasB) {
            $kondisi = 'B';
        }

        $keterangan = $clean($row[20] ?? null);

        // Parse jumlah
        $jumlah_clean = (int) preg_replace('/[^0-9]/', '',
            ($jumlah === '-' || $jumlah === '' || $jumlah === null ? '0' : $jumlah)
        );

        // Parse harga - store as string
        $harga_clean = preg_replace('/[^0-9]/', '',
            ($harga === '-' || $harga === '' || $harga === null ? '0' : $harga)
        );

        return new Barang([
            'ruangan_id'       => $this->ruangan_id,
            'no_urut'          => (int) ($no_urut ?? 0),
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
