<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class PemindahanController extends Controller
{
    public function pindah()
    {
        $barangs = Barang::with('ruangan')->get();
        $ruangans = Ruangan::all();

        return view('pemindahan.pindah', compact('barangs', 'ruangans'));
    }

    public function history()
    {
        // nanti isi log riwayat pemindahan barang
        // untuk sementara kosong dulu

        return view('pemindahan.history');
    }

    public function pindahStore(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'ruangan_tujuan' => 'required|exists:ruangans,id',
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        $barang->ruangan_id = $request->ruangan_tujuan;
        $barang->save();

        return back()->with('success', 'Barang berhasil dipindahkan!');
    }
}
