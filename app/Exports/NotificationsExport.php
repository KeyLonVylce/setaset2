<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NotificationsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $notifications;
    protected $userId;
    protected $statusFilter;
    protected $typeFilter;
    protected $userRole;
    protected $rowNumber = 0;

    public function __construct($notifications, $userId, $statusFilter, $typeFilter, $userRole)
    {
        $this->notifications = $notifications;
        $this->userId = $userId;
        $this->statusFilter = $statusFilter;
        $this->typeFilter = $typeFilter;
        $this->userRole = $userRole;
    }

    public function collection()
    {
        return $this->notifications;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tipe',
            'Aksi',
            'Pesan',
            'Tanggal',
            'Status',
        ];
    }

    public function map($notif): array
    {
        $this->rowNumber++;

        // Cek apakah notifikasi sudah dibaca oleh user yang login
        $readBy = $notif->read_by ?? [];
        $isRead = in_array($this->userId, $readBy);
        $statusText = $isRead ? 'Dibaca' : 'Belum Dibaca';

        return [
            $this->rowNumber,
            $notif->type,
            $notif->aksi,
            strip_tags($notif->pesan),
            $notif->created_at->format('d-m-Y'),
            $statusText,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Baris pertama saat ini adalah heading (baris 1)
                // Kita akan menyisipkan baris baru di atas heading
                
                // Tentukan teks filter status
                $statusText = $this->statusFilter === 'read' ? 'Dibaca' : 
                              ($this->statusFilter === 'unread' ? 'Belum Dibaca' : 'Semua');
                
                // Insert baris untuk filter status
                $sheet->insertNewRowBefore(1, 1);
                $sheet->setCellValue('A1', "Filter Status: {$statusText}");
                $sheet->mergeCells("A1:F1");
                $sheet->getStyle('A1')->getFont()->setBold(true);
                
                // Jika user BUKAN staff, tambahkan baris filter kategori
                if ($this->userRole !== 'staff') {
                    $sheet->insertNewRowBefore(2, 1);
                    $categoryText = ($this->typeFilter && $this->typeFilter !== 'all') ? $this->typeFilter : 'Semua';
                    $sheet->setCellValue('A2', "Filter Kategori: {$categoryText}");
                    $sheet->mergeCells("A2:F2");
                    $sheet->getStyle('A2')->getFont()->setBold(true);
                }
            },
        ];
    }
}