<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KartuInventarisExport implements FromView, WithColumnWidths, WithStyles
{
    protected $ruangan;

    public function __construct($ruangan)
    {
        $this->ruangan = $ruangan;
    }

    public function view(): View
    {
        return view('ruangan.export-excel', [
            'ruangan' => $this->ruangan
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No Urut
            'B' => 25,  // Nama Barang
            'C' => 15,  // Merk/Model
            'D' => 12,  // No Seri
            'E' => 10,  // Ukuran
            'F' => 10,  // Bahan
            'G' => 10,  // Tahun
            'H' => 18,  // Kode Barang
            'I' => 10,  // Jumlah
            'J' => 15,  // Harga
            'K' => 5,   // B
            'L' => 5,   // KB
            'M' => 5,   // RB
            'N' => 20,  // Keterangan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            7 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
        ];
    }
}