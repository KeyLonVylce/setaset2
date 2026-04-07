<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Lantai;
use App\Models\Notification;
use App\Models\PindahBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BarangImport;
use App\Exports\PeriodikExport;
use Illuminate\Pagination\LengthAwarePaginator;

class BarangController extends Controller
{
    // ==================== CRUD BARANG ====================

    public function create($ruangan_id)
    {
        $ruangan = Ruangan::with(['lantai'])->findOrFail($ruangan_id);
        return view('barang.create', compact('ruangan'));
    }

    public function store(Request $request, $ruangan_id)
    {
        $ruangan = Ruangan::with('lantai')->findOrFail($ruangan_id);

        $validated = $request->validate([
            'nama_barang'      => 'required|string|max:255',
            'kode_barang'      => 'nullable|string|max:100',
            'merk_model'       => 'nullable|string|max:255',
            'no_seri_pabrik'   => 'nullable|string|max:255',
            'ukuran'           => 'nullable|string|max:100',
            'bahan'            => 'nullable|string|max:100',
            'tahun_pembuatan'  => 'nullable|integer|min:1900|max:' . date('Y'),
            'jumlah'           => 'required|integer|min:1',
            'kondisi'          => 'required|in:B,KB,RB',
            'harga_perolehan'  => 'nullable|numeric|min:0',
            'total_nilai'      => 'nullable|numeric|min:0',
            'keterangan'       => 'nullable|string',
        ]);

        $validated['ruangan_id'] = $ruangan_id;
        Barang::create($validated);

        $lantaiNama = $ruangan->lantai->nama_lantai ?? 'Lantai tidak diketahui';
        Notification::create([
            'type'        => 'barang',
            'aksi'        => 'tambah',
            'pesan'       => 'Barang <b>' . $validated['nama_barang'] . '</b> ditambahkan ke ruangan <b>' . $ruangan->nama_ruangan . '</b> (' . $lantaiNama . ')',
            'target_role' => 'all',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()->route('ruangan.show', $ruangan_id)
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang  = Barang::findOrFail($id);
        $ruangan = Ruangan::with(['lantai'])->findOrFail($barang->ruangan_id);
        return view('barang.edit', compact('barang', 'ruangan'));
    }

    public function update(Request $request, $id)
    {
        $barang  = Barang::findOrFail($id);
        $ruangan = Ruangan::with('lantai')->findOrFail($barang->ruangan_id);

        $validated = $request->validate([
            'nama_barang'      => 'required|string|max:255',
            'kode_barang'      => 'nullable|string|max:100',
            'merk_model'       => 'nullable|string|max:255',
            'no_seri_pabrik'   => 'nullable|string|max:255',
            'ukuran'           => 'nullable|string|max:100',
            'bahan'            => 'nullable|string|max:100',
            'tahun_pembuatan'  => 'nullable|integer|min:1900|max:' . date('Y'),
            'jumlah'           => 'required|integer|min:1',
            'kondisi'          => 'required|in:B,KB,RB',
            'harga_perolehan'  => 'nullable|numeric|min:0',
            'total_nilai'      => 'nullable|numeric|min:0',
            'keterangan'       => 'nullable|string',
        ]);

        $barang->update($validated);

        $lantaiNama = $ruangan->lantai->nama_lantai ?? 'Lantai tidak diketahui';
        Notification::create([
            'type'        => 'barang',
            'aksi'        => 'edit',
            'pesan'       => 'Barang <b>' . $barang->nama_barang . '</b> diperbarui di ruangan <b>' . $ruangan->nama_ruangan . '</b> (' . $lantaiNama . ')',
            'target_role' => 'all',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()->route('ruangan.show', $barang->ruangan_id)
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang  = Barang::findOrFail($id);
        $ruangan = Ruangan::with('lantai')->findOrFail($barang->ruangan_id);
        $namaBarang = $barang->nama_barang;
        $ruanganId  = $barang->ruangan_id;
        $lantaiNama = $ruangan->lantai->nama_lantai ?? 'Lantai tidak diketahui';

        $barang->delete();

        Notification::create([
            'type'        => 'barang',
            'aksi'        => 'hapus',
            'pesan'       => 'Barang <b>' . $namaBarang . '</b> dihapus dari ruangan <b>' . $ruangan->nama_ruangan . '</b> (' . $lantaiNama . ')',
            'target_role' => 'all',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()->route('ruangan.show', $ruanganId)
            ->with('success', 'Barang berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih.');
        }

        Barang::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', count($ids) . ' barang terpilih berhasil dihapus.');
    }

    // ==================== IMPORT BARANG ====================

    public function importForm($ruangan_id)
    {
        $ruangan = Ruangan::with(['lantai'])->findOrFail($ruangan_id);
        return view('barang.import', compact('ruangan'));
    }

    public function import(Request $request, $ruangan_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $ruangan = Ruangan::with('lantai')->findOrFail($ruangan_id);
        Excel::import(new BarangImport($ruangan_id), $request->file('file'));

        $lantaiNama = $ruangan->lantai->nama_lantai ?? 'Lantai tidak diketahui';
        Notification::create([
            'type'        => 'barang',
            'aksi'        => 'import',
            'pesan'       => 'Import barang ke ruangan <b>' . $ruangan->nama_ruangan . '</b> (' . $lantaiNama . ') berhasil dilakukan.',
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()->route('ruangan.show', $ruangan_id)
            ->with('success', 'Import barang berhasil.');
    }

    // ==================== PEMINDAHAN BARANG ====================

    public function pindahForm()
    {
        $lantais = Lantai::orderBy('urutan')->get();
        $barangs = Barang::with('ruangan')->get();
        $ruangans = Ruangan::with('lantai')->get()->map(fn($r) => [
            'id'           => $r->id,
            'nama_ruangan' => $r->nama_ruangan,
            'lantai_id'    => $r->lantai_id,
            'lantai_nama'  => $r->lantai->nama_lantai ?? '-',
        ]);

        return view('pemindahan.pindah', compact('lantais', 'barangs', 'ruangans'));
    }

    public function pindahStore(Request $request)
    {
        $request->validate([
            'barang_id'       => 'required|exists:barangs,id',
            'ruangan_tujuan'  => 'required|exists:ruangans,id',
            'jumlah_pindah'   => 'required|integer|min:1',
            'notes'           => 'nullable|string',
        ]);

        $barang = Barang::with('ruangan')->findOrFail($request->barang_id);
        $ruanganAsalId = $barang->ruangan_id;
        $jumlahPindah = (int)$request->jumlah_pindah;

        if ($jumlahPindah > $barang->jumlah) {
            return back()->withErrors(['jumlah_pindah' => 'Jumlah yang dipindahkan melebihi stok tersedia!']);
        }

        if ($request->ruangan_tujuan == $barang->ruangan_id) {
            return back()->withErrors(['ruangan_tujuan' => 'Tidak bisa memindahkan ke ruangan yang sama!']);
        }

        $ruanganAsal   = $barang->ruangan->nama_ruangan;
        $ruanganTujuan = Ruangan::findOrFail($request->ruangan_tujuan);

        if ($jumlahPindah == $barang->jumlah) {
            $barang->ruangan_id = $request->ruangan_tujuan;
            $barang->save();
        } else {
            $barang->jumlah -= $jumlahPindah;
            $barang->save();

            $barangTujuan = Barang::where('ruangan_id', $request->ruangan_tujuan)
                ->where('nama_barang', $barang->nama_barang)
                ->where('kode_barang', $barang->kode_barang)
                ->where('merk_model', $barang->merk_model)
                ->first();

            if ($barangTujuan) {
                $barangTujuan->jumlah += $jumlahPindah;
                $barangTujuan->save();
            } else {
                $barangBaru = $barang->replicate();
                $barangBaru->ruangan_id = $request->ruangan_tujuan;
                $barangBaru->jumlah = $jumlahPindah;
                $barangBaru->save();
            }
        }

        PindahBarang::create([
            'barang_id'      => $barang->id,
            'ruangan_asal'   => $ruanganAsalId,
            'ruangan_tujuan' => $request->ruangan_tujuan,
            'jumlah_pindah'  => $jumlahPindah,
            'notes'          => $request->notes,
        ]);

        $message = "Barang <b>{$barang->nama_barang}</b> dipindahkan dari <b>{$ruanganAsal}</b> ke <b>{$ruanganTujuan->nama_ruangan}</b>";
        if ($request->notes) {
            $message .= " | Catatan: {$request->notes}";
        }

        Notification::create([
            'type'        => 'barang',
            'aksi'        => 'pindah',
            'pesan'       => $message,
            'target_role' => 'all',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()->route('pemindahan.laporanpindahbarang')
            ->with('success', 'Barang berhasil dipindahkan!');
    }

    public function laporanPindah()
    {
        $data = PindahBarang::with(['barang', 'asal', 'tujuan'])
            ->latest('created_at')
            ->paginate(20);

        return view('pemindahan.historypindahbarang', compact('data'));
    }

    // ==================== LAPORAN BARANG (Filter) ====================

    public function laporanBarang(Request $request)
    {
        $query = Barang::with([
            'ruangan.lantai',
            'histories.asal',
            'histories.tujuan'
        ]);

        if ($request->lantai) {
            $query->whereHas('ruangan.lantai', function ($q) use ($request) {
                $q->where('id', $request->lantai);
            });
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

        $barangs = $query->get();
        $lantais = Lantai::all();
        $ruangans = Ruangan::all();

        return view('laporan_bulanan.barang', compact('barangs', 'lantais', 'ruangans'));
    }

    public function laporan()
    {
        $data = PindahBarang::with(['barang', 'asal', 'tujuan'])
            ->latest('created_at')
            ->paginate(20);

        return view('pemindahan.historypindahbarang', compact('data'));
    }

    // ==================== LAPORAN PERIODIK ====================


    
    public function periodik(Request $request)
    {
        // 1. Query PindahBarang
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
        if ($request->start_date) {
            $pindahQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $pindahQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->huruf) {
            $pindahQuery->whereHas('barang', fn($q) => $q->where('nama_barang', 'like', $request->huruf . '%'));
        }
    
        $pindahLogs = $pindahQuery->get()->map(fn($p) => [
            'kode_barang'     => $p->barang->kode_barang ?? '-',
            'barang_nama'     => $p->barang->nama_barang ?? '-',
            'aktivitas'       => 'pindah',
            'ruangan_display' => ($p->asal->nama_ruangan ?? '-') . ' → ' . ($p->tujuan->nama_ruangan ?? '-'),
            'created_at'      => $p->created_at,
        ]);
    
        // 2. Query Notification
        $notifQuery = Notification::where('type', 'barang')
            ->whereIn('aksi', ['tambah', 'hapus', 'edit']);
        
        if ($request->bulan) {
            $notifQuery->whereMonth('created_at', (int) $request->bulan);
        }
        if ($request->tahun) {
            $notifQuery->whereYear('created_at', $request->tahun);
        }
        if ($request->start_date) {
            $notifQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $notifQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->huruf) {
            $notifQuery->where('pesan', 'like', $request->huruf . '%');
        }
    
        if ($request->ruangan) {
            $ruanganNama = Ruangan::find($request->ruangan)->nama_ruangan ?? null;
            if ($ruanganNama) {
                $notifQuery->where('pesan', 'like', '%' . $ruanganNama . '%');
            }
        }
    
        $notifLogs = $notifQuery->get()->map(function($n) {
            $cleanPesan = strip_tags($n->pesan);
            $kodeBarang = '-';
            if (preg_match('/\b(KB-\d+)\b/', $cleanPesan, $match)) {
                $kodeBarang = $match[1];
            }
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
                'kode_barang'     => $kodeBarang,
                'barang_nama'     => $namaBarang,
                'aktivitas'       => $n->aksi,
                'ruangan_display' => $ruanganDisplay,
                'created_at'      => $n->created_at,
            ];
        });
    
        // Gabung dan urutkan
        $logs = $pindahLogs->concat($notifLogs)->sortByDesc('created_at')->values();
    
        // Pagination manual (20 per halaman)
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $currentItems = $logs->forPage($currentPage, $perPage);
        $paginator = new LengthAwarePaginator(
            $currentItems,
            $logs->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    
        $lantais  = Lantai::all();
        $ruangans = Ruangan::all();
    
        return view('barang.tabel_periodik', compact('paginator', 'lantais', 'ruangans'));
    }

    // ==================== EXPORT PERIODIK ====================

    public function exportPeriodik(Request $request)
    {
        $filters = [
            'lantai'     => $request->lantai,
            'ruangan'    => $request->ruangan,
            'bulan'      => $request->bulan,
            'tahun'      => $request->tahun,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'huruf'      => $request->huruf,
        ];
    
        return Excel::download(new PeriodikExport($filters), 'tabel_periodik_' . now()->format('Ymd_His') . '.xlsx');
    }
}