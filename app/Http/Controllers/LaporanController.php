<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pindahbarang;
use App\Models\Lantai;
use App\Models\Ruangan;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeriodikExport;

class LaporanController extends Controller
{
    public function barang(Request $request)
    {
        $query = Barang::with(['ruangan.lantai', 'histories.asal', 'histories.tujuan']);

        if ($request->lantai) {
            $query->whereHas('ruangan.lantai', fn($q) => $q->where('id', $request->lantai));
        }
        if ($request->ruangan) {
            $query->where('ruangan_id', $request->ruangan);
        }
        if ($request->huruf) {
            $query->where('nama_barang', 'like', $request->huruf . '%');
        }
        if ($request->bulan) {
            $query->whereMonth('created_at', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('created_at', $request->tahun);
        }

        $barangs  = $query->get();
        $lantais  = Lantai::all();
        $ruangans = Ruangan::all();

        return view('laporan_bulanan.barang', compact('barangs', 'lantais', 'ruangans'));
    }

    public function periodik(Request $request)
    {
        // =============================================
        // 1. PINDAH BARANG
        // =============================================
        $pindahQuery = Pindahbarang::with([
            'barang',
            'asal',
            'asal.lantai',
            'tujuan',
            'tujuan.lantai',
        ]);

        if ($request->bulan) {
            $pindahQuery->whereMonth('created_at', (int) $request->bulan);
        }
        if ($request->tahun) {
            $pindahQuery->whereYear('created_at', $request->tahun);
        }
        if ($request->huruf) {
            $pindahQuery->whereHas('barang', fn($q) =>
                $q->where('nama_barang', 'like', $request->huruf . '%')
            );
        }
        if ($request->lantai) {
            $pindahQuery->where(function ($q) use ($request) {
                $q->whereHas('asal.lantai',     fn($sq) => $sq->where('id', $request->lantai))
                  ->orWhereHas('tujuan.lantai', fn($sq) => $sq->where('id', $request->lantai));
            });
        }
        if ($request->ruangan) {
            $pindahQuery->where(function ($q) use ($request) {
                $q->where('ruangan_asal',    $request->ruangan)
                  ->orWhere('ruangan_tujuan', $request->ruangan);
            });
        }

        $pindahLogs = $pindahQuery->get()->map(function ($p) {

            // Ambil data ruangan asal langsung dari relasi
            $asalRuangan  = $p->asal;
            $tujuRuangan  = $p->tujuan;

            $namaBarang  = $p->barang->nama_barang ?? '-';
            $kodeBarang  = $p->barang->kode_barang ?? null;

            // Jika relasi null, fallback ke query langsung pakai foreign key
            if (!$asalRuangan && $p->ruangan_asal) {
                $asalRuangan = Ruangan::with('lantai')->find($p->ruangan_asal);
            }
            if (!$tujuRuangan && $p->ruangan_tujuan) {
                $tujuRuangan = Ruangan::with('lantai')->find($p->ruangan_tujuan);
            }

            $ruanganAsal = $asalRuangan->nama_ruangan          ?? '-';
            $lantaiAsal  = $asalRuangan->lantai->nama_lantai   ?? '';
            $ruanganTuju = $tujuRuangan->nama_ruangan          ?? '-';
            $lantaiTuju  = $tujuRuangan->lantai->nama_lantai   ?? '';
            $jumlah      = $p->jumlah_pindah ?? 1;
            $notes       = $p->notes ?? null;

            $keterangan  = "Barang <b>{$namaBarang}</b> dipindahkan sebanyak <b>{$jumlah} unit</b> ";
            $keterangan .= "dari ruangan <b>{$ruanganAsal}</b>";
            if ($lantaiAsal) $keterangan .= " ({$lantaiAsal})";
            $keterangan .= " ke ruangan <b>{$ruanganTuju}</b>";
            if ($lantaiTuju) $keterangan .= " ({$lantaiTuju})";
            $keterangan .= ".";
            if ($notes) $keterangan .= " Catatan: <i>{$notes}</i>.";

            return [
                'aktivitas'   => 'pindah',
                'barang_nama' => $namaBarang,
                'kode_barang' => $kodeBarang,
                'dari'        => $ruanganAsal,
                'lantai_dari' => $lantaiAsal,
                'ke'          => $ruanganTuju,
                'lantai_ke'   => $lantaiTuju,
                'keterangan'  => $keterangan,
                // Cast ke string supaya sort dan paginator tidak error
                'created_at'  => (string) $p->created_at,
            ];
        });

        // =============================================
        // 2. NOTIFIKASI (tambah, hapus, edit)
        // =============================================
        $notifQuery = Notification::where('type', 'barang')
            ->whereIn('aksi', ['tambah', 'hapus', 'edit']);

        if ($request->bulan) {
            $notifQuery->whereMonth('created_at', (int) $request->bulan);
        }
        if ($request->tahun) {
            $notifQuery->whereYear('created_at', $request->tahun);
        }
        if ($request->huruf) {
            $notifQuery->where('pesan', 'like', '%>' . $request->huruf . '%');
        }

        $notifLogs = $notifQuery->get()->map(function ($n) {
            preg_match_all('/<b>(.*?)<\/b>/', $n->pesan, $m);
            $namaBarang  = $m[1][0] ?? '-';
            $namaRuangan = $m[1][1] ?? '-';

            $ruanganObj = Ruangan::with('lantai')
                ->where('nama_ruangan', $namaRuangan)
                ->first();
            $namaLantai = $ruanganObj?->lantai?->nama_lantai ?? '';

            if ($n->aksi === 'tambah') {
                $keterangan = "Barang <b>{$namaBarang}</b> ditambahkan ke ruangan <b>{$namaRuangan}</b>" . ($namaLantai ? " ({$namaLantai})" : '') . ".";
                $dari = '-'; $lantaiDari = '';
                $ke   = $namaRuangan; $lantaiKe = $namaLantai;

            } elseif ($n->aksi === 'hapus') {
                $keterangan = "Barang <b>{$namaBarang}</b> dihapus dari ruangan <b>{$namaRuangan}</b>" . ($namaLantai ? " ({$namaLantai})" : '') . ".";
                $dari = $namaRuangan; $lantaiDari = $namaLantai;
                $ke   = '-'; $lantaiKe = '';

            } else {
                $keterangan = "Data barang <b>{$namaBarang}</b> diperbarui di ruangan <b>{$namaRuangan}</b>" . ($namaLantai ? " ({$namaLantai})" : '') . ".";
                $dari = '-'; $lantaiDari = '';
                $ke   = $namaRuangan; $lantaiKe = $namaLantai;
            }

            return [
                'aktivitas'   => $n->aksi,
                'barang_nama' => $namaBarang,
                'kode_barang' => null,
                'dari'        => $dari,
                'lantai_dari' => $lantaiDari,
                'ke'          => $ke,
                'lantai_ke'   => $lantaiKe,
                'keterangan'  => $keterangan,
                // Cast ke string supaya konsisten
                'created_at'  => (string) $n->created_at,
            ];
        });

        // =============================================
        // 3. GABUNG & SORT
        // =============================================
        $logs = $pindahLogs->concat($notifLogs)
            ->sortByDesc('created_at')
            ->values();

        // =============================================
        // 4. PAGINATION MANUAL
        // =============================================
        $perPage     = 20;
        $currentPage = (int) $request->input('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $items       = $logs->slice($offset, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $logs->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $lantais  = Lantai::orderBy('urutan')->get();
        $ruangans = Ruangan::with('lantai')->get()->map(fn($r) => [
            'id'           => $r->id,
            'nama_ruangan' => $r->nama_ruangan,
            'lantai_id'    => $r->lantai_id,
            'lantai_nama'  => $r->lantai->nama_lantai ?? '-',
        ]);

        return view('barang.tabel_periodik', compact('paginator', 'lantais', 'ruangans'));
    }

    public function exportPeriodik(Request $request)
    {
        return Excel::download(new PeriodikExport, 'tabel_periodik.xlsx');
    }
}