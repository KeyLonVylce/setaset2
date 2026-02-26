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
        $ruangans = Ruangan::all();

        return view('pemindahan.pindah', compact('lantais', 'barangs', 'ruangans'));
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
            'jumlah_pindah' => 'required|integer|min:1',
        ]);
    
        $barang = Barang::with('ruangan')->findOrFail($request->barang_id);
        $jumlahPindah = (int)$request->jumlah_pindah;
        
        // Validasi: cek stok cukup atau tidak
        if ($jumlahPindah > $barang->jumlah) {
            return back()->withErrors(['jumlah_pindah' => 'Jumlah yang dipindahkan melebihi stok tersedia!']);
        }
        
        // Validasi: tidak boleh pindah ke ruangan yang sama
        if ($request->ruangan_tujuan == $barang->ruangan_id) {
            return back()->withErrors(['ruangan_tujuan' => 'Tidak bisa memindahkan ke ruangan yang sama!']);
        }
        
        // Simpan ruangan asal
        $ruanganAsal = $barang->ruangan->nama_ruangan;
        
        // Ambil ruangan tujuan
        $ruanganTujuan = Ruangan::findOrFail($request->ruangan_tujuan);
        
        // Cek apakah pindah semua atau sebagian
        if ($jumlahPindah == $barang->jumlah) {
            // PINDAH SEMUA - Update ruangan_id barang
            $barang->ruangan_id = $request->ruangan_tujuan;
            $barang->save();
            
            $message = "Barang <b>{$barang->nama_barang}</b> ({$jumlahPindah} unit) dipindahkan dari <b>{$ruanganAsal}</b> ke <b>{$ruanganTujuan->nama_ruangan}</b>";
        } else {
            // PINDAH SEBAGIAN - Kurangi stok asal & tambah/buat di tujuan
            
            // Kurangi stok di ruangan asal
            $barang->jumlah = $barang->jumlah - $jumlahPindah;
            $barang->save();
            
            // Cek apakah barang yang sama sudah ada di ruangan tujuan
            $barangTujuan = Barang::where('ruangan_id', $request->ruangan_tujuan)
                ->where('nama_barang', $barang->nama_barang)
                ->where('kode_barang', $barang->kode_barang)
                ->where('merk_model', $barang->merk_model)
                ->first();
            
            if ($barangTujuan) {
                // Jika sudah ada, tambah jumlahnya
                $barangTujuan->jumlah = $barangTujuan->jumlah + $jumlahPindah;
                $barangTujuan->save();
            } else {
                // Jika belum ada, buat barang baru
                $barangBaru = $barang->replicate();
                $barangBaru->ruangan_id = $request->ruangan_tujuan;
                $barangBaru->jumlah = $jumlahPindah;
                $barangBaru->save();
            }
            
            $message = "Barang <b>{$barang->nama_barang}</b> sebanyak <b>{$jumlahPindah} unit</b> dipindahkan dari <b>{$ruanganAsal}</b> (sisa: {$barang->jumlah}) ke <b>{$ruanganTujuan->nama_ruangan}</b>";
        }
        
        // Tambahkan catatan jika ada
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