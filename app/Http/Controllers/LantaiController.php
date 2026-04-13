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
    /**
     * Menampilkan halaman home/dashboard
     * - Daftar lantai dengan jumlah ruangan (paginate 4)
     * - Statistik kondisi barang (Baik, Kurang Baik, Rusak Berat)
     * - 5 barang dengan jumlah terbanyak
     */
    public function index()
    {
        // Ambil semua lantai dengan urutan yang sudah ditentukan (scope ordered) dan hitung jumlah ruangan per lantai
        $lantais = Lantai::withCount('ruangans')
            ->ordered()
            ->paginate(4);

        // Hitung total jumlah barang berdasarkan kondisi (B=Baik, KB=Kurang Baik, RB=Rusak Berat)
        $kondisiBaik = Barang::where('kondisi', 'B')->sum('jumlah');
        $kondisiKurangBaik = Barang::where('kondisi', 'KB')->sum('jumlah');
        $kondisiRusakBerat = Barang::where('kondisi', 'RB')->sum('jumlah');
        $totalBarang = $kondisiBaik + $kondisiKurangBaik + $kondisiRusakBerat;

        // Ambil 5 nama barang dengan total jumlah terbanyak (dari seluruh ruangan)
        $topBarangs = Barang::select('nama_barang', DB::raw('SUM(jumlah) as total'))
            ->groupBy('nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Tampilkan view home dengan semua data
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
     * Menampilkan detail lantai beserta daftar ruangan yang ada di dalamnya
     * Mendukung pencarian ruangan berdasarkan nama atau keterangan
     */
    public function show(Request $request, $id)
    {
        // Cari lantai berdasarkan ID, jika tidak ada maka error 404
        $lantai = Lantai::findOrFail($id);

        // Ambil ruangan yang memiliki lantai_id ini, dengan hitung jumlah barang per ruangan
        $ruangans = $lantai->ruangans()
            ->withCount('barangs')
            ->when($request->search, function ($query) use ($request) {
                // Jika ada parameter search, filter ruangan berdasarkan nama_ruangan atau keterangan
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_ruangan', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->paginate(8) // Tampilkan 8 ruangan per halaman
            ->withQueryString(); // Pertahankan parameter search saat paginasi

        return view('lantai.show', compact('lantai', 'ruangans'));
    }

    /**
     * Menyimpan lantai baru (hanya untuk role admin)
     */
    public function store(Request $request)
    {
        // Validasi input: nama_lantai harus unik, maksimal 50 karakter
        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai',
            'keterangan'  => 'nullable|string',
        ]);

        // Buat record lantai baru
        $lantai = Lantai::create([
            'nama_lantai' => $request->nama_lantai,
            'keterangan'  => $request->keterangan,
        ]);

        // Catat notifikasi untuk admin
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
     * Memperbarui data lantai (hanya untuk role admin)
     */
    public function update(Request $request, $id)
    {
        // Cari lantai yang akan diupdate
        $lantai = Lantai::findOrFail($id);

        // Validasi: nama_lantai harus unik kecuali untuk lantai dengan ID ini
        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai,' . $id,
            'keterangan'  => 'nullable|string',
        ]);

        // Update hanya kolom nama_lantai dan keterangan
        $lantai->update($request->only(['nama_lantai', 'keterangan']));

        // Catat notifikasi edit
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
     * Menghapus lantai (hanya untuk role admin)
     * Cek terlebih dahulu apakah lantai masih memiliki ruangan.
     * Jika masih memiliki ruangan, tolak penghapusan.
     */
    public function destroy($id)
    {
        $lantai = Lantai::findOrFail($id);

        // Cegah penghapusan jika lantai masih memiliki ruangan (foreign key constraint)
        if ($lantai->ruangans()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus lantai yang masih memiliki ruangan!');
        }

        $namaLantai = $lantai->nama_lantai;
        $lantai->delete();

        // Catat notifikasi hapus
        Notification::create([
            'type'        => 'lantai',
            'aksi'        => 'hapus',
            'pesan'       => "Lantai <b>{$namaLantai}</b> dihapus",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        // Redirect ke halaman home/dashboard setelah hapus
        return redirect()->route('home')->with('success', 'Lantai berhasil dihapus!');
    }
}