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

    // 🔹 Ubah sesuai posisi data pertama di Excel kamu
    public function startRow(): int
    {
        return 12; // baris pertama data barang (bisa ubah kalau perlu)
    }

    public function model(array $row)
    {
        // 🔹 Fungsi bantu untuk bersihkan nilai kosong / tanda '-'
        $clean = function ($value) {
            if (is_string($value)) {
                $value = trim($value);
                if ($value === '' || $value === '-' || $value === null) {
                    return null;
                }
            }
            return $value;
        };

        // 🔹 Jika baris tidak punya nama barang atau kolom jumlah, abaikan
        if (empty($clean($row[1])) || str_contains(strtolower($row[1]), 'mengetahui')) {
            return null;
        }

        // 🔹 Abaikan baris tanda tangan atau teks (bukan barang)
        $nonBarangKeywords = ['mengetahui', 'sekretaris', 'nip', 'provinsi', 'penanggungjawab', 'bayu', 's.stp', 'mh', 'handi', 'suhanda'];
        foreach ($nonBarangKeywords as $keyword) {
            if (str_contains(strtolower($row[1]), $keyword)) {
                return null;
            }
        }

        // 🔹 Simpan hanya baris yang mengandung nama barang valid
        return new Barang([
            'ruangan_id'          => $this->ruangan_id,
            'no_urut'             => (int)($clean($row[0]) ?? 0),
            'nama_barang'         => $clean($row[1]),
            'merk_model'          => $clean($row[2]),
            'no_seri_pabrik'      => $clean($row[3]),
            'ukuran'              => $clean($row[4]),
            'bahan'               => $clean($row[5]),
            'tahun_pembuatan'     => is_numeric($clean($row[6])) ? $clean($row[6]) : null,
            'kode_barang'         => $clean($row[7]),
            'jumlah'              => (int)($clean($row[8]) ?? 0),
            'harga_perolehan'     => (float)($clean($row[9]) ?? 0),
            'keadaan_baik'        => (int)($clean($row[10]) ?? 0),
            'keadaan_kurang_baik' => (int)($clean($row[11]) ?? 0),
            'keadaan_rusak_berat' => (int)($clean($row[12]) ?? 0),
            'keterangan'          => $clean($row[13]),
        ]);
    }
}
