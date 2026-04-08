<?php

namespace App\Exports;

use App\Models\Pindahbarang;
use App\Models\Notification;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeriodikExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // 1. Query PindahBarang dengan filter
        $pindahQuery = Pindahbarang::with(['barang', 'asal', 'tujuan']);

        if (!empty($this->filters['lantai'])) {
            $pindahQuery->whereHas('tujuan.lantai', fn($q) => $q->where('id', $this->filters['lantai']));
        }
        if (!empty($this->filters['ruangan'])) {
            $pindahQuery->where('ruangan_tujuan', $this->filters['ruangan']);
        }
        if (!empty($this->filters['bulan'])) {
            $pindahQuery->whereMonth('created_at', (int) $this->filters['bulan']);
        }
        if (!empty($this->filters['tahun'])) {
            $pindahQuery->whereYear('created_at', $this->filters['tahun']);
        }
        if (!empty($this->filters['start_date'])) {
            $pindahQuery->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $pindahQuery->whereDate('created_at', '<=', $this->filters['end_date']);
        }
        if (!empty($this->filters['huruf'])) {
            $pindahQuery->whereHas('barang', fn($q) => $q->where('nama_barang', 'like', $this->filters['huruf'] . '%'));
        }

        $pindahLogs = $pindahQuery->get()->map(fn($p) => [
            'kode_barang'     => $p->barang->kode_barang ?? '-',
            'barang_nama'     => $p->barang->nama_barang ?? '-',
            'aktivitas'       => 'pindah',
            'ruangan_display' => ($p->asal->nama_ruangan ?? '-') . ' → ' . ($p->tujuan->nama_ruangan ?? '-'),
            'created_at'      => $p->created_at,
        ]);

        // 2. Query Notification dengan filter
        $notifQuery = Notification::where('type', 'barang')
            ->whereIn('aksi', ['tambah', 'hapus', 'edit']);

        if (!empty($this->filters['bulan'])) {
            $notifQuery->whereMonth('created_at', (int) $this->filters['bulan']);
        }
        if (!empty($this->filters['tahun'])) {
            $notifQuery->whereYear('created_at', $this->filters['tahun']);
        }
        if (!empty($this->filters['start_date'])) {
            $notifQuery->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $notifQuery->whereDate('created_at', '<=', $this->filters['end_date']);
        }
        if (!empty($this->filters['huruf'])) {
            $notifQuery->where('pesan', 'like', $this->filters['huruf'] . '%');
        }
        if (!empty($this->filters['ruangan'])) {
            $ruanganNama = Ruangan::find($this->filters['ruangan'])->nama_ruangan ?? null;
            if ($ruanganNama) {
                $notifQuery->where('pesan', 'like', '%' . $ruanganNama . '%');
            }
        }

        $notifLogs = $notifQuery->get()->map(function ($n) {
            $cleanPesan = strip_tags($n->pesan);
            $namaBarang = '-';
            if (preg_match('/Barang\s+(.+?)\s+(di|ke|dari)/i', $cleanPesan, $matches)) {
                $namaBarang = trim($matches[1]);
            } else {
                if (preg_match('/Barang\s+(.+?)(\.|$)/i', $cleanPesan, $matches)) {
                    $namaBarang = trim($matches[1]);
                }
            }

            $ruangan = '-';
            if (preg_match('/(ke|dari|di)\s+ruangan\s+(.+?)(\.|$)/i', $cleanPesan, $matches)) {
                $ruangan = trim($matches[2]);
                $ruangan = preg_replace('/\s*\([^)]+\)/', '', $ruangan);
                $ruangan = trim($ruangan);
            }
            $ruanganDisplay = $ruangan !== '-' ? $ruangan : '-';

            return [
                'kode_barang'     => '-',
                'barang_nama'     => $namaBarang,
                'aktivitas'       => $n->aksi,
                'ruangan_display' => $ruanganDisplay,
                'created_at'      => $n->created_at,
            ];
        });

        $logs = $pindahLogs->concat($notifLogs)->sortByDesc('created_at')->values();

        return $logs;
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Aktivitas',
            'Ruangan (Asal → Tujuan / Lokasi)',
            'Tanggal & Waktu'
        ];
    }

    public function map($log): array
    {
        return [
            $log['kode_barang'],
            $log['barang_nama'],
            ucfirst($log['aktivitas']),
            $log['ruangan_display'],
            $log['created_at']->format('d-m-Y H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}