<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PindahBarang;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeriodikExport;

class LaporanController extends Controller
{
    public function barang(Request $request)
    {
        $query = Barang::with([
            'ruangan.lantai',
            'histories.asal',
            'histories.tujuan'
        ]);

        // 🔥 FILTER LANTAI
        if ($request->lantai) {
            $query->whereHas('ruangan.lantai', function ($q) use ($request) {
                $q->where('id', $request->lantai);
            });
        }

        // 🔥 FILTER RUANGAN
        if ($request->ruangan) {
            $query->where('ruangan_id', $request->ruangan);
        }

        // 🔥 FILTER HURUF
        if ($request->huruf) {
            $query->where('nama_barang', 'like', $request->huruf . '%');
        }

        // 🔥 FILTER BULAN & TAHUN
        if ($request->bulan) {
            $query->whereMonth('created_at', $request->bulan);
        }

        if ($request->tahun) {
            $query->whereYear('created_at', $request->tahun);
        }

        $barangs = $query->get();

        $lantais = Lantai::all();
        $ruangans = Ruangan::all();

        return view('laporan_bulanan.barang', compact('barangs', 'lantais', 'ruangans'));
    }

        public function periodik(Request $request)
        {
            // === 1. Query PindahBarang (aktivitas: pindah) ===
            $pindahQuery = PindahBarang::with(['barang', 'asal', 'tujuan']);

            if ($request->lantai) {
                $pindahQuery->whereHas('tujuan.lantai', fn($q) => $q->where('id', $request->lantai));
            }
            if ($request->ruangan) {
                $pindahQuery->where('ruangan_tujuan', $request->ruangan);
            }
            if ($request->bulan) {
                $pindahQuery->whereMonth('created_at', (int) $request->bulan);
            }
            if ($request->tahun) {
                $pindahQuery->whereYear('created_at', $request->tahun);
            }
            if ($request->huruf) {
                $pindahQuery->whereHas('barang', fn($q) => $q->where('nama_barang', 'like', $request->huruf . '%'));
            }

            $pindahLogs = $pindahQuery->get()->map(fn($p) => [
                'barang_nama'   => $p->barang->nama_barang ?? '-',
                'aktivitas'     => 'pindah',
                'dari'          => $p->asal->nama_ruangan ?? '-',
                'ke'            => $p->tujuan->nama_ruangan ?? '-',
                'created_at'    => $p->created_at,
            ]);

            // === 2. Query Notification (aktivitas: tambah, hapus, edit) ===
            
            $notifQuery = \App\Models\Notification::where('type', 'barang')
                ->whereIn('aksi', ['tambah', 'hapus', 'edit']);

            if ($request->bulan) {
                $notifQuery->whereMonth('created_at', (int) $request->bulan);
            }
            if ($request->tahun) {
                $notifQuery->whereYear('created_at', $request->tahun);
            }
            if ($request->huruf) {
                $notifQuery->where('pesan', 'like', $request->huruf . '%');
            }

            $notifLogs = $notifQuery->get()->map(fn($n) => [
                'barang_nama'   => '-',   // parse dari pesan jika perlu
                'aktivitas'     => $n->aksi,
                'dari'          => '-',
                'ke'            => '-',
                'pesan'         => $n->pesan,
                'created_at'    => $n->created_at,
            ]);

            // === 3. Gabung & Sort ===
            $logs = $pindahLogs->concat($notifLogs)
                ->sortByDesc('created_at')
                ->values();

            $lantais  = \App\Models\Lantai::all();
            $ruangans = \App\Models\Ruangan::all();

            return view('barang.tabel_periodik', compact('logs', 'lantais', 'ruangans'));
        }

    // 🔥 3. EXPORT EXCEL PERIODIK
    public function exportPeriodik()
    {
        return Excel::download(new PeriodikExport, 'tabel_periodik.xlsx');
    }
}