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
        // Data starts at row 12 (1‑based index)
        return 12;
    }

    public function model(array $row)
    {
        // Helper to clean and nullify empty/dash values
        $clean = function ($value) {
            if (is_string($value)) {
                $v = trim($value);
                return ($v === '' || $v === '-' ? null : $v);
            }
            return $value;
        };

        // Column A (index 0) = NO URUT → skip
        $nama_barang = $clean($row[1] ?? null);
        if (empty($nama_barang)) {
            return null; // skip rows without a name
        }

        // Basic fields
        $merk_model     = $clean($row[3] ?? null);
        $no_seri_pabrik = $clean($row[4] ?? null);
        $ukuran         = $clean($row[5] ?? null);
        $bahan          = $clean($row[6] ?? null);
        $tahun          = $clean($row[7] ?? null);

        // Kode barang: columns I (8) to N (13), concatenated with dots
        $kode_parts = [];
        for ($i = 8; $i <= 14; $i++) {
            $part = $clean($row[$i] ?? null);
            if ($part !== null) {
                $kode_parts[] = $part;
            }
        }
        $kode_barang = !empty($kode_parts) ? implode('.', $kode_parts) : null;

        // Jumlah and Harga (columns O and P)
        $jumlah = $clean($row[15] ?? null);
        $harga  = $clean($row[16] ?? null);

        // ---------- Determine Kondisi ----------
        // Check columns: Q (KEADAAN BARANG), R (B), S (KB), T (RB)
        $checkValue = function ($val) {
            if (is_string($val)) {
                $val = trim($val);
                // Direct match
                if (in_array($val, ['B', 'KB', 'RB'])) {
                    return $val;
                }
                // Match values like (B), (KB), (RB)
                if (preg_match('/\(([BKR]+)\)/', $val, $matches)) {
                    $code = $matches[1];
                    if (in_array($code, ['B', 'KB', 'RB'])) {
                        return $code;
                    }
                }
            }
            return null;
        };

        $kondisi = null;
        // Check each column, first non‑null wins
        $candidates = [
            $checkValue($row[17] ?? null), // R
            $checkValue($row[18] ?? null), // S
            $checkValue($row[19] ?? null), // T
        ];
        foreach ($candidates as $candidate) {
            if ($candidate) {
                $kondisi = $candidate;
                break;
            }
        }

        // Keterangan (column U)
        $keterangan = $clean($row[20] ?? null);

        // Parse numeric values (strip non‑digits)
        $jumlah_clean = (int) preg_replace('/[^0-9]/', '', ($jumlah ?? '0'));
        $harga_clean  = preg_replace('/[^0-9]/', '', ($harga ?? '0'));
        $harga_clean  = $harga_clean ? (float) $harga_clean : null;

        // Build the Barang model
        return new Barang([
            'ruangan_id'       => $this->ruangan_id,
            'nama_barang'      => $nama_barang,
            'merk_model'       => $merk_model,
            'no_seri_pabrik'   => $no_seri_pabrik,
            'ukuran'           => $ukuran,
            'bahan'            => $bahan,
            'tahun_pembuatan'  => is_numeric($tahun) ? (int) $tahun : null,
            'kode_barang'      => $kode_barang,
            'jumlah'           => $jumlah_clean,
            'harga_perolehan'  => $harga_clean,
            'kondisi'          => $kondisi,
            'keterangan'       => $keterangan ?: "-",
        ]);
    }
}