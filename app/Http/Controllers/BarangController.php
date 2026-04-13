<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Lantai;
use App\Models\Notification;
use App\Models\Pindahbarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BarangImport;
use App\Exports\PeriodikExport;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

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

    public function laporan()
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

    // FUNGSI BANTU UNTUK FILTER TANGGAL
    $applyDateFilter = function ($query, $startDate, $endDate) {
        if (empty($startDate) && empty($endDate)) {
            return false; // tidak ada filter tanggal
        }

        try {
            if (!empty($startDate) && !empty($endDate)) {
                // kedua tanggal diisi
                $start = Carbon::parse($startDate)->startOfDay();
                $end   = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } elseif (!empty($startDate)) {
                // hanya start_date
                $start = Carbon::parse($startDate)->startOfDay();
                $query->where('created_at', '>=', $start);
            } elseif (!empty($endDate)) {
                // hanya end_date
                $end = Carbon::parse($endDate)->endOfDay();
                $query->where('created_at', '<=', $end);
            }
            return true; // filter tanggal diterapkan
        } catch (\Exception $e) {
            // format tanggal tidak valid, abaikan filter tanggal
            return false;
        }
    };

    // Ambil nilai dari request
    $startDate = $request->input('start_date');
    $endDate   = $request->input('end_date');

    // Terapkan filter tanggal ke pindahQuery
    $dateFilterApplied = $applyDateFilter($pindahQuery, $startDate, $endDate);

    // Jika filter tanggal TIDAK diterapkan, baru gunakan filter bulan/tahun
    if (!$dateFilterApplied) {
        if ($request->filled('bulan')) {
            $pindahQuery->whereMonth('created_at', (int) $request->bulan);
        }
        if ($request->filled('tahun')) {
            $pindahQuery->whereYear('created_at', $request->tahun);
        }
    }

    // Filter lainnya (huruf, lantai, ruangan) - tidak berubah
    if ($request->filled('huruf')) {
        $pindahQuery->whereHas('barang', fn($q) =>
            $q->where('nama_barang', 'like', $request->huruf . '%')
        );
    }
    if ($request->filled('lantai')) {
        $pindahQuery->where(function ($q) use ($request) {
            $q->whereHas('asal.lantai',     fn($sq) => $sq->where('id', $request->lantai))
              ->orWhereHas('tujuan.lantai', fn($sq) => $sq->where('id', $request->lantai));
        });
    }
    if ($request->filled('ruangan')) {
        $pindahQuery->where(function ($q) use ($request) {
            $q->where('ruangan_asal',    $request->ruangan)
              ->orWhere('ruangan_tujuan', $request->ruangan);
        });
    }

    $pindahLogs = $pindahQuery->get()->map(function ($p) {
        // ... (kode Anda tetap sama, tidak diubah)
        $asalRuangan  = $p->asal;
        $tujuRuangan  = $p->tujuan;
        $namaBarang   = $p->barang->nama_barang ?? '-';
        $kodeBarang   = $p->barang->kode_barang ?? null;

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
            'created_at'  => (string) $p->created_at,
        ];
    });

    // =============================================
    // 2. NOTIFIKASI (tambah, hapus, edit)
    // =============================================
    $notifQuery = Notification::where('type', 'barang')
        ->whereIn('aksi', ['tambah', 'hapus', 'edit']);

    // Terapkan filter tanggal yang sama ke notifQuery
    $dateFilterAppliedNotif = $applyDateFilter($notifQuery, $startDate, $endDate);

    if (!$dateFilterAppliedNotif) {
        if ($request->filled('bulan')) {
            $notifQuery->whereMonth('created_at', (int) $request->bulan);
        }
        if ($request->filled('tahun')) {
            $notifQuery->whereYear('created_at', $request->tahun);
        }
    }

    if ($request->filled('huruf')) {
        $notifQuery->where('pesan', 'like', '%>' . $request->huruf . '%');
    }

    // Filter notifikasi berdasarkan lantai/ruangan
    if ($request->filled('lantai') || $request->filled('ruangan')) {
        $ruanganQuery = Ruangan::query();
        if ($request->filled('lantai')) {
            $ruanganQuery->where('lantai_id', $request->lantai);
        }
        if ($request->filled('ruangan')) {
            $ruanganQuery->where('id', $request->ruangan);
        }
        $namaRuangans = $ruanganQuery->pluck('nama_ruangan')->toArray();

        $notifQuery->where(function ($q) use ($namaRuangans) {
            foreach ($namaRuangans as $nama) {
                $q->orWhere('pesan', 'like', '%<b>' . $nama . '</b>%');
            }
        });
    }

    $notifLogs = $notifQuery->get()->map(function ($n) {
        // ... (kode Anda tetap sama, tidak diubah)
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
            'created_at'  => (string) $n->created_at,
        ];
    });

    // Filter tambahan lantai/ruangan untuk notifikasi
    if ($request->filled('lantai')) {
        $namaLantaiDipilih = Lantai::find($request->lantai)?->nama_lantai;
        $notifLogs = $notifLogs->filter(function ($log) use ($namaLantaiDipilih) {
            return $log['lantai_dari'] === $namaLantaiDipilih || $log['lantai_ke'] === $namaLantaiDipilih;
        })->values();
    }

    if ($request->filled('ruangan')) {
        $namaRuanganDipilih = Ruangan::find($request->ruangan)?->nama_ruangan;
        $notifLogs = $notifLogs->filter(function ($log) use ($namaRuanganDipilih) {
            return $log['dari'] === $namaRuanganDipilih || $log['ke'] === $namaRuanganDipilih;
        })->values();
    }

    // =============================================
    // 3. GABUNG & PAGINATION
    // =============================================
    $logs = $pindahLogs->concat($notifLogs)
        ->sortByDesc('created_at')
        ->values();

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
    
        return Excel::download(new PeriodikExport($filters), 'tabel_periodik_' . now()->format('Ymd') . '.xlsx');
    }
}