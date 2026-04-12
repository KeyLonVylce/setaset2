<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Lantai;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LantaiController extends Controller
{
    public function index()
    {
        $lantais = Lantai::withCount('ruangans')
            ->ordered()
            ->paginate(4);

        // Hitung kondisi barang global
        $kondisiBaik = Barang::where('kondisi', 'B')->sum('jumlah');
        $kondisiKurangBaik = Barang::where('kondisi', 'KB')->sum('jumlah');
        $kondisiRusakBerat = Barang::where('kondisi', 'RB')->sum('jumlah');
        $totalBarang = $kondisiBaik + $kondisiKurangBaik + $kondisiRusakBerat;

        // Ambil 5 barang terbanyak
        $topBarangs = Barang::select('nama_barang', DB::raw('SUM(jumlah) as total'))
            ->groupBy('nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('home', compact(
            'lantais',
            'kondisiBaik',
            'kondisiKurangBaik',
            'kondisiRusakBerat',
            'totalBarang',
            'topBarangs'
        ));
    }

    /**
     * Menampilkan detail lantai beserta ruangan-ruangan
     */
    public function show(Request $request, $id)
    {
        $lantai = Lantai::findOrFail($id);

        $ruangans = $lantai->ruangans()
            ->withCount('barangs')
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_ruangan', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->paginate(8)
            ->withQueryString();

        return view('lantai.show', compact('lantai', 'ruangans'));
    }

    /**
     * Store lantai baru (admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai',
            'keterangan'  => 'nullable|string',
        ]);

        $lantai = Lantai::create([
            'nama_lantai' => $request->nama_lantai,
            'keterangan'  => $request->keterangan,
        ]);

        Notification::create([
            'type'        => 'lantai',
            'aksi'        => 'tambah',
            'pesan'       => "Lantai <b>{$lantai->nama_lantai}</b> ditambahkan",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Lantai berhasil ditambahkan!');
    }

    /**
     * Update lantai (admin)
     */
    public function update(Request $request, $id)
    {
        $lantai = Lantai::findOrFail($id);

        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai,' . $id,
            'keterangan'  => 'nullable|string',
        ]);

        $lantai->update($request->only(['nama_lantai', 'keterangan']));

        Notification::create([
            'type'        => 'lantai',
            'aksi'        => 'edit',
            'pesan'       => "Lantai <b>{$lantai->nama_lantai}</b> diubah",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Lantai berhasil diupdate!');
    }

    /**
     * Hapus lantai (admin)
     */
    public function destroy($id)
    {
        $lantai = Lantai::findOrFail($id);

        if ($lantai->ruangans()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus lantai yang masih memiliki ruangan!');
        }

        $namaLantai = $lantai->nama_lantai;
        $lantai->delete();

        Notification::create([
            'type'        => 'lantai',
            'aksi'        => 'hapus',
            'pesan'       => "Lantai <b>{$namaLantai}</b> dihapus",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()->route('home')->with('success', 'Lantai berhasil dihapus!');
    }
}