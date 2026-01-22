<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lantai;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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

    public function storeLantai(Request $request)
    {
        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai',
            'urutan' => 'nullable|integer',
            'keterangan' => 'nullable|string',
        ]);

        $urutan = $request->urutan ?? (Lantai::max('urutan') ?? 0) + 1;

        Lantai::create([
            'nama_lantai' => $request->nama_lantai,
            'urutan' => $urutan,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Lantai berhasil ditambahkan!');
    }
}