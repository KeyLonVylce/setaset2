<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Lantai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemindahanController extends Controller
{
    public function pindah()
    {
        $lantais = Lantai::orderBy('urutan')->get();
        $barangs = Barang::with('ruangan')->get();

        // FIX: load relasi lantai lalu map ke array biasa
        // supaya JS tidak terima Eloquent object -> [object Object]
        $ruangans = Ruangan::with('lantai')->get()->map(fn($r) => [
            'id'           => $r->id,
            'nama_ruangan' => $r->nama_ruangan,
            'lantai_id'    => $r->lantai_id,
            'lantai_nama'  => $r->lantai->nama_lantai ?? '-',
        ]);

        return view('pemindahan.pindah', compact('lantais', 'barangs', 'ruangans'));
    }

    public function history()
    {
        return view('pemindahan.history');
    }

    public function pindahStore(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'ruangan_tujuan' => 'required|exists:ruangans,id',
            'jumlah_pindah' => 'required|integer|min:1',
        ]);

        $barang = Barang::with('ruangan')->findOrFail($request->barang_id);
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
            $message = "Barang <b>{$barang->nama_barang}</b> ({$jumlahPindah} unit) dipindahkan dari <b>{$ruanganAsal}</b> ke <b>{$ruanganTujuan->nama_ruangan}</b>";
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
                $barangBaru             = $barang->replicate();
                $barangBaru->ruangan_id = $request->ruangan_tujuan;
                $barangBaru->jumlah     = $jumlahPindah;
                $barangBaru->save();
            }

            $message = "Barang <b>{$barang->nama_barang}</b> sebanyak <b>{$jumlahPindah} unit</b> dipindahkan dari <b>{$ruanganAsal}</b> (sisa: {$barang->jumlah}) ke <b>{$ruanganTujuan->nama_ruangan}</b>";
        }

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

        return redirect()->route('home')->with('success', 'Barang berhasil dipindahkan!');
    }
}