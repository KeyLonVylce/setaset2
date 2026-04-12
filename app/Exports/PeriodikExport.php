<?php

namespace App\Exports;

use App\Models\Pindahbarang;
use App\Models\Notification;
use App\Models\Ruangan;
use App\Models\Lantai;
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
        // 1. Query PindahBarang
        $pindahQuery = Pindahbarang::with(['barang', 'asal', 'asal.lantai', 'tujuan', 'tujuan.lantai']);

        if (!empty($this->filters['lantai'])) {
            $pindahQuery->where(function ($q) {
                $q->whereHas('asal.lantai',    fn($sq) => $sq->where('id', $this->filters['lantai']))
                ->orWhereHas('tujuan.lantai', fn($sq) => $sq->where('id', $this->filters['lantai']));
            });
        }
        if (!empty($this->filters['ruangan'])) {
            $pindahQuery->where(function ($q) {
                $q->where('ruangan_asal',    $this->filters['ruangan'])
                ->orWhere('ruangan_tujuan', $this->filters['ruangan']);
            });
        }
        if (!empty($this->filters['bulan'])) {
            $pindahQuery->whereMonth('created_at', (int) $this->filters['bulan']);
        }
        if (!empty($this->filters['tahun'])) {
            $pindahQuery->whereYear('created_at', $this->filters['tahun']);
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

        // 2. Query Notifikasi
        $notifQuery = Notification::where('type', 'barang')
            ->whereIn('aksi', ['tambah', 'hapus', 'edit']);

        if (!empty($this->filters['bulan'])) {
            $notifQuery->whereMonth('created_at', (int) $this->filters['bulan']);
        }
        if (!empty($this->filters['tahun'])) {
            $notifQuery->whereYear('created_at', $this->filters['tahun']);
        }
        if (!empty($this->filters['huruf'])) {
            $notifQuery->where('pesan', 'like', '%>' . $this->filters['huruf'] . '%');
        }

        // Filter notif berdasarkan nama ruangan di lantai/ruangan yang dipilih
        if (!empty($this->filters['lantai']) || !empty($this->filters['ruangan'])) {
            $ruanganQuery = Ruangan::query();
            if (!empty($this->filters['lantai'])) {
                $ruanganQuery->where('lantai_id', $this->filters['lantai']);
            }
            if (!empty($this->filters['ruangan'])) {
                $ruanganQuery->where('id', $this->filters['ruangan']);
            }
            $namaRuangans = $ruanganQuery->pluck('nama_ruangan')->toArray();

            $notifQuery->where(function ($q) use ($namaRuangans) {
                foreach ($namaRuangans as $nama) {
                    $q->orWhere('pesan', 'like', '%<b>' . $nama . '</b>%');
                }
            });
        }

        $notifLogs = $notifQuery->get()->map(function ($n) {
            preg_match_all('/<b>(.*?)<\/b>/', $n->pesan, $m);
            $namaBarang  = $m[1][0] ?? '-';
            $namaRuangan = $m[1][1] ?? '-';

            $ruanganObj = Ruangan::with('lantai')
                ->where('nama_ruangan', $namaRuangan)
                ->first();
            $namaLantai = $ruanganObj?->lantai?->nama_lantai ?? '';

            $ruanganDisplay = $namaRuangan . ($namaLantai ? " ({$namaLantai})" : '');

            return [
                'kode_barang'     => '-',
                'barang_nama'     => $namaBarang,
                'aktivitas'       => $n->aksi,
                'ruangan_display' => $ruanganDisplay,
                'created_at'      => $n->created_at,
            ];
        });

        // Filter ketat by lantai setelah map
        if (!empty($this->filters['lantai'])) {
            $namaLantaiDipilih = Lantai::find($this->filters['lantai'])?->nama_lantai;
            $notifLogs = $notifLogs->filter(function ($log) use ($namaLantaiDipilih) {
                return str_contains($log['ruangan_display'], $namaLantaiDipilih);
            })->values();
        }

        if (!empty($this->filters['ruangan'])) {
            $namaRuanganDipilih = Ruangan::find($this->filters['ruangan'])?->nama_ruangan;
            $notifLogs = $notifLogs->filter(function ($log) use ($namaRuanganDipilih) {
                return str_contains($log['ruangan_display'], $namaRuanganDipilih);
            })->values();
        }

        return $pindahLogs->concat($notifLogs)
            ->sortByDesc('created_at')
            ->values();
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Aktivitas',
            'Ruangan (Asal → Tujuan / Lokasi)',
            'Tanggal'
        ];
    }

    public function map($log): array
    {
        return [
            $log['kode_barang'],
            $log['barang_nama'],
            ucfirst($log['aktivitas']),
            $log['ruangan_display'],
            $log['created_at']->format('d-m-Y')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}