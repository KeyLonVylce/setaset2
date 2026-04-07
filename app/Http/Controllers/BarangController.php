<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Lantai;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BarangImport;
use App\Models\Pindahbarang;

class BarangController extends Controller
{
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

        \App\Models\Notification::create([
            'type'        => 'barang',
            'aksi'        => 'tambah',
            'pesan'       => 'Barang <b>' . $validated['nama_barang'] . '</b> ditambahkan ke ruangan <b>' . $ruangan->nama_ruangan . '</b> (' . $lantaiNama . ')',
            'target_role' => 'all',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()
            ->route('ruangan.show', $ruangan_id)
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

        \App\Models\Notification::create([
            'type'        => 'barang',
            'aksi'        => 'edit',
            'pesan'       => 'Barang <b>' . $barang->nama_barang . '</b> diperbarui di ruangan <b>' . $ruangan->nama_ruangan . '</b> (' . $lantaiNama . ')',
            'target_role' => 'all',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()
            ->route('ruangan.show', $barang->ruangan_id)
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang  = Barang::findOrFail($id);
        $ruangan = Ruangan::with('lantai')->findOrFail($barang->ruangan_id);

        $namaBarang  = $barang->nama_barang;
        $ruanganId   = $barang->ruangan_id;
        $lantaiNama  = $ruangan->lantai->nama_lantai ?? 'Lantai tidak diketahui';

        $barang->delete();

        \App\Models\Notification::create([
            'type'        => 'barang',
            'aksi'        => 'hapus',
            'pesan'       => 'Barang <b>' . $namaBarang . '</b> dihapus dari ruangan <b>' . $ruangan->nama_ruangan . '</b> (' . $lantaiNama . ')',
            'target_role' => 'all',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()
            ->route('ruangan.show', $ruanganId)
            ->with('success', 'Barang berhasil dihapus.');
    }

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

        \App\Models\Notification::create([
            'type'        => 'barang',
            'aksi'        => 'import',
            'pesan'       => 'Import barang ke ruangan <b>' . $ruangan->nama_ruangan . '</b> (' . $lantaiNama . ') berhasil dilakukan.',
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return redirect()
            ->route('ruangan.show', $ruangan_id)
            ->with('success', 'Import barang berhasil.');
    }

    /**
     * Form pemindahan barang
     */
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

    public function laporan()
    {
        $data = PindahBarang::with(['barang', 'asal', 'tujuan'])
            ->latest('created_at')
            ->paginate(20);

        return view('pemindahan.historypindahbarang', compact('data'));
    }

    /**
     * Proses pemindahan barang
     */
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
                $barangBaru             = $barang->replicate();
                $barangBaru->ruangan_id = $request->ruangan_tujuan;
                $barangBaru->jumlah     = $jumlahPindah;
                $barangBaru->save();
            }
        }

        Pindahbarang::create([
            'barang_id'      => $barang->id,
            'ruangan_asal'   => $ruanganAsalId,
            'ruangan_tujuan' => $request->ruangan_tujuan,
            'jumlah_pindah'  => $jumlahPindah,
            'notes'          => $request->notes,
        ]);

        // NOTIF
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

        return redirect()->route('pemindahan.laporanpindahbarang')->with('success', 'Barang berhasil dipindahkan!');
    }

    /**
     * History pemindahan (bisa diimplementasikan nanti)
     */
    public function history()
    {
        // Bisa pakai view history pemindahan
        return view('barang.history');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');

        // Jika ids adalah string (misal "1,2,3"), ubah menjadi array
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        // Hapus nilai kosong
        $ids = array_filter($ids);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih.');
        }

        // Pastikan semua ID adalah integer
        $ids = array_map('intval', $ids);

        // Hapus barang
        Barang::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', count($ids) . ' barang terpilih berhasil dihapus.');
    }
}