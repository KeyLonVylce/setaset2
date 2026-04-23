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
    // Menyimpan filter yang dikirim dari request (lantai, ruangan, bulan, tahun, start_date, end_date, huruf)
    protected $filters;

    // Konstruktor: menerima array filter dari controller
    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Menerapkan filter rentang tanggal (start_date - end_date) ke query builder.
     * Mengembalikan true jika filter tanggal berhasil diterapkan, false jika tidak.
     */
    private function applyDateFilter($query, $startDate, $endDate)
    {
        $hasStart = !empty($startDate);
        $hasEnd   = !empty($endDate);

        // Jika tidak ada filter tanggal sama sekali, langsung false
        if (!$hasStart && !$hasEnd) {
            return false;
        }

        try {
            if ($hasStart && $hasEnd) {
                // Kedua tanggal diisi: filter between
                $start = Carbon::parse($startDate)->startOfDay();
                $end   = Carbon::parse($endDate)->endOfDay();
                if ($start <= $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                    return true;
                }
            } elseif ($hasStart) {
                // Hanya start_date
                $start = Carbon::parse($startDate)->startOfDay();
                $query->where('created_at', '>=', $start);
                return true;
            } elseif ($hasEnd) {
                // Hanya end_date
                $end = Carbon::parse($endDate)->endOfDay();
                $query->where('created_at', '<=', $end);
                return true;
            }
        } catch (\Exception $e) {
            // Format tanggal tidak valid -> abaikan filter
        }

        return false;
    }

    /**
     * Mengumpulkan semua data (pindah barang + notifikasi) yang akan diexport
     * Menerapkan filter yang sudah disimpan di $this->filters
     */
    public function collection()
    {
        // ========== PINDAH BARANG ==========
        // Query awal untuk data pemindahan barang beserta relasi barang, asal, tujuan, dan lantai
        $pindahQuery = Pindahbarang::with(['barang', 'asal.lantai', 'tujuan.lantai']);

        // Terapkan filter rentang tanggal (prioritas utama)
        $dateFilterApplied = $this->applyDateFilter(
            $pindahQuery,
            $this->filters['start_date'] ?? null,
            $this->filters['end_date'] ?? null
        );

        // Jika tidak ada filter rentang tanggal, baru gunakan filter bulan/tahun
        if (!$dateFilterApplied) {
            if (!empty($this->filters['bulan'])) {
                $pindahQuery->whereMonth('created_at', (int) $this->filters['bulan']);
            }
            if (!empty($this->filters['tahun'])) {
                $pindahQuery->whereYear('created_at', $this->filters['tahun']);
            }
        }

        // Filter lantai (berlaku untuk asal atau tujuan)
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

        // Filter huruf awal nama barang
        if (!empty($this->filters['huruf'])) {
            $pindahQuery->whereHas('barang', fn($q) => $q->where('nama_barang', 'like', $this->filters['huruf'] . '%'));
        }
        if (!empty($this->filters['kondisi'])) {
            $pindahQuery->whereHas('barang', fn($q) => $q->where('kondisi', $this->filters['kondisi']));
        }

        // Eksekusi query dan mapping hasil ke format yang seragam
        $pindahLogs = $pindahQuery->get()->map(function ($p) {
            // Format asal ruangan (termasuk lantai)
            $asalNama = $p->asal->nama_ruangan ?? '-';
            $asalLantai = $p->asal->lantai->nama_lantai ?? '';
            $dari = $asalNama . ($asalLantai ? " ({$asalLantai})" : '');

            // Format tujuan ruangan (termasuk lantai)
            $tujuanNama = $p->tujuan->nama_ruangan ?? '-';
            $tujuanLantai = $p->tujuan->lantai->nama_lantai ?? '';
            $ke = $tujuanNama . ($tujuanLantai ? " ({$tujuanLantai})" : '');

            return (object) [
                'kode_barang' => $p->barang->kode_barang ?? '-',
                'barang_nama' => $p->barang->nama_barang ?? '-',
                'aktivitas'   => 'pindah',
                'kondisi'     => $p->barang->kondisi ?? null,
                'dari'        => $dari,
                'ke'          => $ke,
                'created_at'  => $p->created_at,
            ];
        });

        // ========== NOTIFIKASI (tambah, hapus, edit barang) ==========
        $notifQuery = Notification::where('type', 'barang')
            ->whereIn('aksi', ['tambah', 'hapus', 'edit']);

        // Terapkan filter tanggal yang sama untuk notifikasi
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

        // Filter huruf pada pesan notifikasi (mencari kata setelah '>' untuk nama barang)
        if (!empty($this->filters['huruf'])) {
            $notifQuery->where('pesan', 'like', '%>' . $this->filters['huruf'] . '%');
        }

        // Filter notifikasi berdasarkan lantai atau ruangan dengan mencocokkan nama ruangan dalam tag <b>
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
                // Tidak ada ruangan yang cocok → hasil kosong
                $notifQuery->whereRaw('1 = 0');
            }
        }

        // Mapping notifikasi ke format yang sama
        $notifLogs = $notifQuery->get()->map(function ($n) {
            // Ekstrak nama barang dan nama ruangan dari tag <b>
            preg_match_all('/<b>(.*?)<\/b>/', $n->pesan, $matches);
            $namaBarang  = $matches[1][0] ?? '-';
            $namaRuangan = $matches[1][1] ?? '-';

            // Cari objek ruangan untuk mendapatkan lantai
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

            $barangObj = \App\Models\Barang::query()
                ->when($namaBarang !== '-', fn($q) => $q->where('nama_barang', $namaBarang))
                ->when($namaRuangan !== '-', function ($q) use ($namaRuangan) {
                    $q->whereHas('ruangan', fn($sq) => $sq->where('nama_ruangan', $namaRuangan));
                })
                ->latest('updated_at')
                ->first();

            return (object) [
                'kode_barang' => '-',
                'barang_nama' => $namaBarang,
                'aktivitas'   => $n->aksi,
                'kondisi'     => $barangObj?->kondisi,
                'dari'        => $dari,
                'ke'          => $ke,
                'created_at'  => $n->created_at,
            ];
        });

        // Filter ulang notifikasi berdasarkan lantai (karena sebelumnya hanya berdasarkan nama ruangan)
        if (!empty($this->filters['lantai'])) {
            $namaLantaiDipilih = Lantai::find($this->filters['lantai'])?->nama_lantai;
            if ($namaLantaiDipilih) {
                $notifLogs = $notifLogs->filter(function ($log) use ($namaLantaiDipilih) {
                    return str_contains($log->dari, $namaLantaiDipilih) || str_contains($log->ke, $namaLantaiDipilih);
                })->values();
            }
        }

        // Filter ulang notifikasi berdasarkan ruangan
        if (!empty($this->filters['ruangan'])) {
            $namaRuanganDipilih = Ruangan::find($this->filters['ruangan'])?->nama_ruangan;
            if ($namaRuanganDipilih) {
                $notifLogs = $notifLogs->filter(function ($log) use ($namaRuanganDipilih) {
                    return str_contains($log->dari, $namaRuanganDipilih) || str_contains($log->ke, $namaRuanganDipilih);
                })->values();
            }
        }

        if (!empty($this->filters['kondisi'])) {
            $notifLogs = $notifLogs->filter(function ($log) {
                return ($log->kondisi ?? null) === $this->filters['kondisi'];
            })->values();
        }

        // Gabungkan semua log, urutkan dari yang terbaru
        $allLogs = $pindahLogs->concat($notifLogs)->sortByDesc('created_at')->values();

        if (!empty($this->filters['aktivitas'])) {
            $allLogs = $allLogs->filter(function ($log) {
                return $log->aktivitas === $this->filters['aktivitas'];
            })->values();
        }

        return $allLogs;
    }

    /**
     * Header kolom untuk file Excel
     */
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

    /**
     * Mapping setiap baris data ke array yang sesuai dengan urutan heading
     */
    public function map($log): array
    {
        return [
            $log->barang_nama,
            $log->kode_barang,
            ucfirst($log->aktivitas), // huruf pertama kapital (Pindah, Tambah, Hapus, Edit)
            $log->dari,
            $log->ke,
            $log->created_at->format('d-m-Y') // format tanggal Indonesia
        ];
    }

    /**
     * Styling untuk worksheet Excel (baris pertama tebal)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
