<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KartuInventarisExport implements WithEvents
{
    protected $ruangan;

    public function __construct($ruangan)
    {
        $this->ruangan = $ruangan;
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                // 1. Load template
                $templatePath = $path = public_path('template/barang_template.xlsx');
                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();

                // 2. Isi data header
                $sheet->setCellValue('D6', $this->ruangan->nama_ruangan); // Nama Ruangan
                // Kode Lokasi (asumsikan merge cell S6:U6)
                $sheet->setCellValue('S6', 'NO. KODE LOKASI : ' . ($this->ruangan->kode_lokasi ?? '11.10.00.21.01.25'));

                // 3. Tentukan baris awal data (setelah header tabel)
                // Template: baris 8 = header utama, baris 9 = subheader, baris 10 = angka kolom
                // Data dimulai dari baris 11
                $startRow = 12;
                $currentRow = $startRow;

                // Hapus baris data lama jika ada
                $highestRow = $sheet->getHighestRow();
                if ($highestRow >= $startRow) {
                    $sheet->removeRow($startRow, $highestRow - $startRow + 1);
                }

                // 4. Isi data barang
                foreach ($this->ruangan->barangs as $index => $barang) {
                    $sheet->setCellValue('A' . $currentRow, $index + 1); // No Urut
                    $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
                    $sheet->setCellValue('B' . $currentRow, $barang->nama_barang);
                    $sheet->setCellValue('D' . $currentRow, $barang->merk_model ?? '');
                    $sheet->setCellValue('E' . $currentRow, $barang->no_seri_pabrik ?? '');
                    $sheet->setCellValue('F' . $currentRow, $barang->ukuran ?? '');
                    $sheet->setCellValue('G' . $currentRow, $barang->bahan ?? '');
                    $sheet->setCellValue('H' . $currentRow, $barang->tahun_pembuatan ?? '');
                    $kodeParts = explode('.', (string) $barang->kode_barang);

                    for ($i = 0; $i < 7; $i++) {
                        $column = chr(ord('I') + $i); // I sampai O
                        $sheet->setCellValue($column . $currentRow, $kodeParts[$i] ?? '');
                    }
                    $sheet->setCellValue('P' . $currentRow, $barang->jumlah); // Jumlah Register
                    
                    $sheet->setCellValue('Q' . $currentRow, (int) $barang->harga_perolehan);
                    $sheet->getStyle('Q' . $currentRow)
                          ->getNumberFormat()
                          ->setFormatCode('#,##0');
                    
    
                    // Kondisi
                    $kondisi = strtoupper(trim($barang->kondisi));
                    $sheet->setCellValue('R' . $currentRow, $kondisi === 'B' ? 'B' : '');
                    $sheet->setCellValue('S' . $currentRow, $kondisi === 'KB' ? 'KB' : '');
                    $sheet->setCellValue('T' . $currentRow, $kondisi === 'RB' ? 'RB' : '');
                    $sheet->setCellValue('U' . $currentRow, $barang->keterangan); // Keterangan mutasi
                    
                    $currentRow++;
                }

                // 5. Terapkan border untuk area data
                $lastColumn = 'U';
                $lastRow = $currentRow - 1;
                if ($lastRow >= $startRow) {
                    $styleArray = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ];
                    $sheet->getStyle('A' . $startRow . ':' . $lastColumn . $lastRow)->applyFromArray($styleArray);
                }

                // 6. Ganti spreadsheet di writer dengan yang sudah dimodifikasi
                $event->writer->getDelegate()->setActiveSheetIndex(0);
                $event->writer->getDelegate()->removeSheetByIndex(0);
                $event->writer->getDelegate()->addExternalSheet($sheet);

                return $spreadsheet;
            },
        ];
    }
}