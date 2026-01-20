<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use App\Helpers\NotificationHelper;

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
    
        $barang = Barang::with('ruangan')->findOrFail($request->barang_id);
    
        // simpan ruangan asal
        $ruanganAsal = $barang->ruangan->nama_ruangan;
    
        // ambil ruangan tujuan
        $ruanganTujuan = Ruangan::findOrFail($request->ruangan_tujuan)->nama_ruangan;
    
        // update barang
        $barang->ruangan_id = $request->ruangan_tujuan;
        $barang->save();
    
        NotificationHelper::create(
            'barang',
            'pindah',
            "Barang <b>{$barang->nama_barang}</b> dipindahkan dari ruangan <b>{$ruanganAsal}</b> ke ruangan <b>{$ruanganTujuan}</b>"
        );
    
        return back()->with('success', 'Barang berhasil dipindahkan!');
    }
    
}
