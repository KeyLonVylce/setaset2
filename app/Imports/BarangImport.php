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

        // Abaikan baris tanda tangan / footer
        $ignore = ['mengetahui', 'sekretaris', 'nip', 'provinsi', 'penanggungjawab'];
        if (!empty($row[1])) {
            foreach ($ignore as $kw) {
                if (str_contains(strtolower($row[1]), $kw)) {
                    return null;
                }
            }
        }

        // Jika tidak ada nama barang → skip
        if (empty($clean($row[1]))) {
            return null;
        }

        // ==========================
        //  MAPPING KOLOM EXCEL
        // ==========================
        $no_urut         = $clean($row[0]);        // Kolom A (NO URUT)
        $nama_barang     = $clean($row[1]);        // Kolom B (NAMA BARANG/JENIS BARANG)
        $merk_model      = $clean($row[3]);        // Kolom D (MERK/MODEL)
        $no_seri_pabrik  = $clean($row[4]);        // Kolom E (No. SERI PABRIK)
        $ukuran          = $clean($row[5]);        // Kolom F (UKURAN)
        $bahan           = $clean($row[6]);        // Kolom G (BAHAN)
        $tahun           = $clean($row[7]);        // Kolom H (TAHUN PEMBUATAN/PEMBELIAN)
        $kode_barang = implode('.', array_filter([
            $clean($row[9] ?? null),
            $clean($row[10] ?? null),
            $clean($row[11] ?? null),
            $clean($row[12] ?? null),
            $clean($row[13] ?? null),
            $clean($row[14] ?? null),
        ], fn($v) => $v !== null && $v !== '' && $v !== '-'));        // Kolom I (NO. KODE BARANG) - hanya kolom I saja

        $jumlah = $clean($row[15]);   // Kolom Q (JUMLAH BARANG)
        $harga  = $clean($row[16]);   // Kolom R (HARGA BELI/PEROLEHAN)        
        
        // Untuk kondisi barang, ambil dari kolom S, T, U
        $kondisi_b       = $clean($row[17]);       // Kolom S (BAIK)
        $kondisi_kb      = $clean($row[18]);       // Kolom T (KURANG BAIK)  
        $kondisi_rb      = $clean($row[19]);       // Kolom U (RUSAK BERAT)

        // Tentukan kondisi berdasarkan mana yang berisi "(B)"
        if (in_array(strtoupper(str_replace(['(',')'], '', $kondisi_b)), ['B'])) {
            $kondisi = "B";
        } elseif (in_array(strtoupper(str_replace(['(',')'], '', $kondisi_kb)), ['KB'])) {
            $kondisi = "KB";
        } elseif (in_array(strtoupper(str_replace(['(',')'], '', $kondisi_rb)), ['RB'])) {
            $kondisi = "RB";
        } else {
            $kondisi = null; // default
        }
        

        // Keterangan kolom terakhir
        $keterangan      = $clean($row[20] ?? null);  // Kolom V (KETERANGAN MUTASI DLL)
        return new Barang([
            'ruangan_id'       => $this->ruangan_id,
            'no_urut'          => (int) ($no_urut ?? 0),
            'nama_barang'      => $nama_barang,
            'merk_model'       => $merk_model,
            'no_seri_pabrik'   => $no_seri_pabrik,
            'ukuran'           => $ukuran,
            'bahan'            => $bahan,
            'tahun_pembuatan'  => is_numeric($tahun) ? $tahun : null,
            'kode_barang'      => $kode_barang,

            'jumlah' => (int) preg_replace('/[^0-9]/', '', 
                ($jumlah === '-' || $jumlah === '' || $jumlah === null ? '0' : $jumlah)
            ),

            'harga_perolehan' => (int) preg_replace('/[^0-9]/', '', 
                ($harga === '-' || $harga === '' || $harga === null ? '0' : $harga)
            ),

            'kondisi'          => $kondisi,  // default 'null'
            'keterangan'       => $keterangan,
        ]);
    }
}
