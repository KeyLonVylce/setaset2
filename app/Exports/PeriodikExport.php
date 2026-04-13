<?php

namespace App\Exports;

use App\Models\Pindahbarang;
use App\Models\Notification;
use App\Models\Ruangan;
use App\Models\Lantai;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PeriodikExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Apply date range filter to a query.
     * Returns true if a valid date filter was applied, false otherwise.
     */
    private function applyDateFilter($query, $startDate, $endDate)
    {
        $hasStart = !empty($startDate);
        $hasEnd   = !empty($endDate);

        if (!$hasStart && !$hasEnd) {
            return false;
        }

        try {
            if ($hasStart && $hasEnd) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end   = Carbon::parse($endDate)->endOfDay();
                if ($start <= $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                    return true;
                }
            } elseif ($hasStart) {
                $start = Carbon::parse($startDate)->startOfDay();
                $query->where('created_at', '>=', $start);
                return true;
            } elseif ($hasEnd) {
                $end = Carbon::parse($endDate)->endOfDay();
                $query->where('created_at', '<=', $end);
                return true;
            }
        } catch (\Exception $e) {
            // Invalid date format – ignore filter
        }

        return false;
    }

    public function collection()
    {
        // ========== PINDAH BARANG ==========
        $pindahQuery = Pindahbarang::with(['barang', 'asal.lantai', 'tujuan.lantai']);

        // Apply date range filter (priority over month/year)
        $dateFilterApplied = $this->applyDateFilter(
            $pindahQuery,
            $this->filters['start_date'] ?? null,
            $this->filters['end_date'] ?? null
        );

        // Only use month/year if no date range was applied
        if (!$dateFilterApplied) {
            if (!empty($this->filters['bulan'])) {
                $pindahQuery->whereMonth('created_at', (int) $this->filters['bulan']);
            }
            if (!empty($this->filters['tahun'])) {
                $pindahQuery->whereYear('created_at', $this->filters['tahun']);
            }
        }

        // Filter lantai (asal atau tujuan)
        if (!empty($this->filters['lantai'])) {
            $pindahQuery->where(function ($q) {
                $q->whereHas('asal.lantai', fn($sq) => $sq->where('id', $this->filters['lantai']))
                  ->orWhereHas('tujuan.lantai', fn($sq) => $sq->where('id', $this->filters['lantai']));
            });
        }

        // Filter ruangan (asal atau tujuan)
        if (!empty($this->filters['ruangan'])) {
            $pindahQuery->where(function ($q) {
                $q->where('ruangan_asal', $this->filters['ruangan'])
                  ->orWhere('ruangan_tujuan', $this->filters['ruangan']);
            });
        }

        // Filter huruf (nama barang)
        if (!empty($this->filters['huruf'])) {
            $pindahQuery->whereHas('barang', fn($q) => $q->where('nama_barang', 'like', $this->filters['huruf'] . '%'));
        }

        $pindahLogs = $pindahQuery->get()->map(function ($p) {
            $asalNama = $p->asal->nama_ruangan ?? '-';
            $asalLantai = $p->asal->lantai->nama_lantai ?? '';
            $dari = $asalNama . ($asalLantai ? " ({$asalLantai})" : '');

            $tujuanNama = $p->tujuan->nama_ruangan ?? '-';
            $tujuanLantai = $p->tujuan->lantai->nama_lantai ?? '';
            $ke = $tujuanNama . ($tujuanLantai ? " ({$tujuanLantai})" : '');

            return (object) [
                'kode_barang' => $p->barang->kode_barang ?? '-',
                'barang_nama' => $p->barang->nama_barang ?? '-',
                'aktivitas'   => 'pindah',
                'dari'        => $dari,
                'ke'          => $ke,
                'created_at'  => $p->created_at,
            ];
        });

        // ========== NOTIFIKASI ==========
        $notifQuery = Notification::where('type', 'barang')
            ->whereIn('aksi', ['tambah', 'hapus', 'edit']);

        // Apply same date filter to notifications
        $dateFilterAppliedNotif = $this->applyDateFilter(
            $notifQuery,
            $this->filters['start_date'] ?? null,
            $this->filters['end_date'] ?? null
        );

        if (!$dateFilterAppliedNotif) {
            if (!empty($this->filters['bulan'])) {
                $notifQuery->whereMonth('created_at', (int) $this->filters['bulan']);
            }
            if (!empty($this->filters['tahun'])) {
                $notifQuery->whereYear('created_at', $this->filters['tahun']);
            }
        }

        // Filter huruf on notification message
        if (!empty($this->filters['huruf'])) {
            $notifQuery->where('pesan', 'like', '%>' . $this->filters['huruf'] . '%');
        }

        // Filter notifikasi berdasarkan lantai/ruangan (parse from HTML)
        if (!empty($this->filters['lantai']) || !empty($this->filters['ruangan'])) {
            $ruanganQuery = Ruangan::query();
            if (!empty($this->filters['lantai'])) {
                $ruanganQuery->where('lantai_id', $this->filters['lantai']);
            }
            if (!empty($this->filters['ruangan'])) {
                $ruanganQuery->where('id', $this->filters['ruangan']);
            }
            $namaRuangans = $ruanganQuery->pluck('nama_ruangan')->toArray();

            if (count($namaRuangans) > 0) {
                $notifQuery->where(function ($q) use ($namaRuangans) {
                    foreach ($namaRuangans as $nama) {
                        $q->orWhere('pesan', 'like', '%<b>' . $nama . '</b>%');
                    }
                });
            } else {
                // No matching rooms → force empty result
                $notifQuery->whereRaw('1 = 0');
            }
        }

        $notifLogs = $notifQuery->get()->map(function ($n) {
            preg_match_all('/<b>(.*?)<\/b>/', $n->pesan, $matches);
            $namaBarang  = $matches[1][0] ?? '-';
            $namaRuangan = $matches[1][1] ?? '-';

            $ruanganObj = Ruangan::with('lantai')
                ->where('nama_ruangan', $namaRuangan)
                ->first();
            $namaLantai = $ruanganObj?->lantai?->nama_lantai ?? '';
            $ruanganDisplay = $namaRuangan . ($namaLantai ? " ({$namaLantai})" : '');

            $dari = '-';
            $ke   = '-';
            if ($n->aksi === 'hapus') {
                $dari = $ruanganDisplay;
            } elseif ($n->aksi === 'tambah' || $n->aksi === 'edit') {
                $ke = $ruanganDisplay;
            }

            return (object) [
                'kode_barang' => '-',
                'barang_nama' => $namaBarang,
                'aktivitas'   => $n->aksi,
                'dari'        => $dari,
                'ke'          => $ke,
                'created_at'  => $n->created_at,
            ];
        });

        // Post-filter notifications by lantai/ruangan (if needed)
        if (!empty($this->filters['lantai'])) {
            $namaLantaiDipilih = Lantai::find($this->filters['lantai'])?->nama_lantai;
            if ($namaLantaiDipilih) {
                $notifLogs = $notifLogs->filter(function ($log) use ($namaLantaiDipilih) {
                    return str_contains($log->dari, $namaLantaiDipilih) || str_contains($log->ke, $namaLantaiDipilih);
                })->values();
            }
        }

        if (!empty($this->filters['ruangan'])) {
            $namaRuanganDipilih = Ruangan::find($this->filters['ruangan'])?->nama_ruangan;
            if ($namaRuanganDipilih) {
                $notifLogs = $notifLogs->filter(function ($log) use ($namaRuanganDipilih) {
                    return str_contains($log->dari, $namaRuanganDipilih) || str_contains($log->ke, $namaRuanganDipilih);
                })->values();
            }
        }

        // Merge and sort
        $allLogs = $pindahLogs->concat($notifLogs)->sortByDesc('created_at')->values();

        return $allLogs;
    }

    public function headings(): array
    {
        return [
            'Barang',
            'Kode',
            'Aktivitas',
            'Dari',
            'Ke',
            'Tanggal'
        ];
    }

    public function map($log): array
    {
        return [
            $log->barang_nama,
            $log->kode_barang,
            ucfirst($log->aktivitas),
            $log->dari,
            $log->ke,
            $log->created_at->format('d-m-Y')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}