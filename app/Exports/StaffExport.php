<?php

namespace App\Exports;

use App\Models\StafAset;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * Ambil data staff dengan role 'staff' saja, urut descending created_at
     */
    public function query()
    {
        return StafAset::where('role', 'staff')->orderBy('created_at', 'desc');
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Username',
            'Nama',
            'NIP',
            'Email',
            'Role',
            'Tanggal Dibuat',
        ];
    }

    /**
     * Mapping tiap baris data ke kolom Excel
     * $row adalah object StafAset
     * $key adalah index (mulai 0)
     */
    public function map($row): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $row->username,
            $row->nama,
            $row->nip,
            $row->email,
            $row->role_label ?? 'staff',
            $row->created_at->locale('id')->translatedFormat('d F Y')
        ];
    }

    /**
     * Styling header (bold)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}