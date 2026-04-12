<?php

namespace App\Exports;

use App\Models\Pindahbarang;
use App\Models\Notification;
use App\Models\Ruangan;
use App\Models\Lantai;
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
        // ==================== 1. PINDAH BARANG ====================
        $pindahQuery = Pindahbarang::with(['barang', 'asal.lantai', 'tujuan.lantai']);

        if (!empty($this->filters['lantai'])) {
            $pindahQuery->where(function ($q) {
                $q->whereHas('asal.lantai', fn($sq) => $sq->where('id', $this->filters['lantai']))
                  ->orWhereHas('tujuan.lantai', fn($sq) => $sq->where('id', $this->filters['lantai']));
            });
        }
        if (!empty($this->filters['ruangan'])) {
            $pindahQuery->where(function ($q) {
                $q->where('ruangan_asal', $this->filters['ruangan'])
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

        $pindahLogs = $pindahQuery->get()->map(function ($p) {
            // Format asal (Dari)
            $asalNama = $p->asal->nama_ruangan ?? '-';
            $asalLantai = $p->asal->lantai->nama_lantai ?? '';
            $dari = $asalNama . ($asalLantai ? " ({$asalLantai})" : '');

            // Format tujuan (Ke)
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

        // ==================== 2. NOTIFIKASI (TAMBAH, EDIT, HAPUS) ====================
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

        // Filter notifikasi berdasarkan lantai/ruangan (jika ada)
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
            // Ambil nama barang dan ruangan dari tag <b>
            preg_match_all('/<b>(.*?)<\/b>/', $n->pesan, $matches);
            $namaBarang  = $matches[1][0] ?? '-';
            $namaRuangan = $matches[1][1] ?? '-';

            // Cari objek ruangan untuk mendapatkan lantai
            $ruanganObj = Ruangan::with('lantai')
                ->where('nama_ruangan', $namaRuangan)
                ->first();
            $namaLantai = $ruanganObj?->lantai?->nama_lantai ?? '';
            $ruanganDisplay = $namaRuangan . ($namaLantai ? " ({$namaLantai})" : '');

            // Tentukan kolom Dari dan Ke berdasarkan aksi
            $dari = '-';
            $ke   = '-';
            if ($n->aksi === 'hapus') {
                $dari = $ruanganDisplay;
            } elseif ($n->aksi === 'tambah' || $n->aksi === 'edit') {
                $ke = $ruanganDisplay;
            }

            return (object) [
                'kode_barang' => '-',                     // Notifikasi tidak menyimpan kode barang
                'barang_nama' => $namaBarang,
                'aktivitas'   => $n->aksi,
                'dari'        => $dari,
                'ke'          => $ke,
                'created_at'  => $n->created_at,
            ];
        });

        // Filter tambahan untuk notifikasi berdasarkan lantai (karena lantai tidak tersimpan langsung)
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

        // ==================== 3. GABUNG & URUTKAN ====================
        return $pindahLogs->concat($notifLogs)
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Heading (judul kolom) sesuai tampilan halaman
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
     * Mapping setiap baris data ke kolom Excel
     */
    public function map($log): array
    {
        return [
            $log->barang_nama,
            $log->kode_barang,
            ucfirst($log->aktivitas),          // pindah, tambah, edit, hapus
            $log->dari,
            $log->ke,
            $log->created_at->format('d-m-Y') // format tanggal
        ];
    }

    /**
     * Styling header (baris pertama) bold
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}