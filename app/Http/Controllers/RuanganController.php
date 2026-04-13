<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Barang;
use App\Models\Lantai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KartuInventarisExport;

class RuanganController extends Controller
{
    /**
     * Menampilkan detail ruangan + daftar barang
     * Mendukung pencarian barang, sorting nama_barang, dan pagination
     */
    public function show(Request $request, $id)
    {
        // Ambil data ruangan beserta relasi lantai, jika tidak ada error 404
        $ruangan = Ruangan::with(['lantai'])->findOrFail($id);
    
        // Query dasar untuk mengambil barang di ruangan ini
        $query = Barang::where('ruangan_id', $id);
    
        // Sorting berdasarkan nama_barang (default asc, bisa desc dari request)
        $sortBy = 'nama_barang'; 
        $direction = $request->get('direction', 'asc'); // ambil parameter direction, default asc
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc'; // pastikan hanya asc atau desc
        }
    
        // Jika ada parameter search, lakukan pencarian di beberapa kolom barang
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('merk_model', 'like', "%{$search}%")
                  ->orWhere('no_seri_pabrik', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }
    
        // Ambil barang dengan sorting dan pagination (20 per halaman), pertahankan parameter query string
        $barangs = $query->orderBy($sortBy, $direction)->paginate(20)->withQueryString();
        
        // ✅ PERBAIKAN: kirim variabel direction ke view (untuk keperluan sorting toggle)
        return view('ruangan.show', compact('ruangan', 'barangs', 'direction'));
    }

    /**
     * Store ruangan baru (hanya admin)
     * Ruangan ditambahkan ke lantai tertentu berdasarkan $lantai_id
     */
    public function store(Request $request, $lantai_id)
    {
        // Pastikan lantai dengan ID ini ada
        $lantai = Lantai::findOrFail($lantai_id);

        // Validasi input
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
        ]);

        // Buat record ruangan baru
        $ruangan = Ruangan::create([
            'lantai_id'    => $lantai_id,
            'nama_ruangan' => $request->nama_ruangan,
            'keterangan'   => $request->keterangan,
        ]);

        // Catat notifikasi untuk admin
        Notification::create([
            'type'        => 'ruangan',
            'aksi'        => 'tambah',
            'pesan'       => "Ruangan <b>{$ruangan->nama_ruangan}</b> ditambahkan di lantai <b>{$lantai->nama_lantai}</b>",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Ruangan berhasil ditambahkan!');
    }

    /**
     * Update ruangan (hanya admin)
     */
    public function update(Request $request, $id)
    {
        // Cari ruangan berdasarkan ID
        $ruangan = Ruangan::findOrFail($id);

        // Validasi input
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
        ]);

        // Update data ruangan
        $ruangan->update([
            'nama_ruangan' => $request->nama_ruangan,
            'keterangan'   => $request->keterangan,
        ]);

        // Catat notifikasi edit
        Notification::create([
            'type'        => 'ruangan',
            'aksi'        => 'edit',
            'pesan'       => "Ruangan <b>{$ruangan->nama_ruangan}</b> di lantai <b>{$ruangan->lantai->nama_lantai}</b> diubah",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Ruangan berhasil diupdate!');
    }

    /**
     * Hapus ruangan (hanya admin)
     * Cek terlebih dahulu apakah ruangan masih memiliki barang.
     * Jika masih memiliki barang, tolak penghapusan.
     */
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        // Cegah penghapusan jika masih ada barang di ruangan ini
        if ($ruangan->barangs()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus ruangan yang masih memiliki barang!');
        }

        $namaRuangan = $ruangan->nama_ruangan;
        $namaLantai  = $ruangan->lantai->nama_lantai;

        $ruangan->delete();

        // Catat notifikasi hapus
        Notification::create([
            'type'        => 'ruangan',
            'aksi'        => 'hapus',
            'pesan'       => "Ruangan <b>{$namaRuangan}</b> dihapus dari lantai <b>{$namaLantai}</b>",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Ruangan berhasil dihapus!');
    }

    /**
     * Export kartu inventaris ruangan ke Excel atau tampilkan view print
     * Jika format=excel, download file Excel; selain itu tampilkan view export untuk print/PDF
     */
    public function export(Request $request, $id)
    {
        // Ambil ruangan beserta semua barangnya
        $ruangan = Ruangan::with('barangs')->findOrFail($id);

        // Export Excel
        if ($request->input('format') === 'excel') {
            $filename = 'Kartu_Inventaris_' 
                . str_replace(' ', '_', $ruangan->nama_ruangan) 
                . '_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new KartuInventarisExport($ruangan),
                $filename
            );
        }

        // View print / PDF (biasanya akan dipanggil dari button Print atau generate PDF)
        return view('ruangan.export', compact('ruangan'));
    }

    /**
     * Generate dan download PDF kartu inventaris ruangan
     * Menggunakan DomPDF dengan orientasi landscape kertas A4
     */
    public function exportPdf($id)
    {
        // Ambil data ruangan
        $ruangan = Ruangan::findOrFail($id);

        // Buat PDF dari view 'ruangan.export', kirim flag pdf=true untuk menyesuaikan tampilan
        $pdf = PDF::loadView('ruangan.export', [
            'ruangan' => $ruangan,
            'pdf' => true
        ])->setPaper('a4', 'landscape');

        // Download file PDF dengan nama dinamis
        return $pdf->download('ruangan-'.$ruangan->nama_ruangan.'.pdf');
    }
}