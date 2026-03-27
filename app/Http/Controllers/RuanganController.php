<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Barang;
use App\Models\Lantai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Notification;

class RuanganController extends Controller
{
    /**
     * Menampilkan detail ruangan + daftar barang
     */
    public function show(Request $request, $id)
    {
        $ruangan = Ruangan::with(['lantai'])->findOrFail($id);

        $query = Barang::where('ruangan_id', $id);

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

        $barangs = $query->paginate(20)->withQueryString();

        return view('ruangan.show', compact('ruangan', 'barangs'));
    }

    /**
     * Store ruangan baru (hanya admin)
     */
    public function store(Request $request, $lantai_id)
    {
        $lantai = Lantai::findOrFail($lantai_id);

        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id'    => $lantai_id,
            'nama_ruangan' => $request->nama_ruangan,
            'keterangan'   => $request->keterangan,
        ]);

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
        $ruangan = Ruangan::findOrFail($id);

        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
        ]);

        $ruangan->update([
            'nama_ruangan' => $request->nama_ruangan,
            'keterangan'   => $request->keterangan,
        ]);

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
     */
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        if ($ruangan->barangs()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus ruangan yang masih memiliki barang!');
        }

        $namaRuangan = $ruangan->nama_ruangan;
        $namaLantai  = $ruangan->lantai->nama_lantai;

        $ruangan->delete();

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
     * Export PDF kartu inventaris (hanya admin)
     */
    public function export($id)
    {
        $ruangan = Ruangan::with(['lantai', 'barangs'])->findOrFail($id);

        $pdf = Pdf::loadView('ruangan.export', compact('ruangan'))
                  ->setPaper('a4', 'landscape');

        $filename = 'kartu-inventaris-' . str_replace(' ', '-', strtolower($ruangan->nama_ruangan)) . '.pdf';

        return $pdf->download($filename);
    }
}