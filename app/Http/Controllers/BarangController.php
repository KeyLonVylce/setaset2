<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BarangImport;

class BarangController extends Controller
{
    public function create($ruangan_id)
    {
        $ruangan = Ruangan::with(['lantai', 'penanggungJawab'])->findOrFail($ruangan_id);

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

        // Notifikasi
        $lantaiNama = $ruangan->lantai->nama_lantai ?? 'Lantai tidak diketahui';

        \App\Models\Notification::create([
            'stafaset_id' => Auth::guard('stafaset')->id(),
            'message'     => 'Barang baru "' . $validated['nama_barang'] . '" ditambahkan ke ruangan ' . $ruangan->nama_ruangan . ' (' . $lantaiNama . ')',
            'type'        => 'barang_added',
        ]);

        return redirect()
            ->route('ruangan.show', $ruangan_id)
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang  = Barang::findOrFail($id);
        $ruangan = Ruangan::with(['lantai', 'penanggungJawab'])->findOrFail($barang->ruangan_id);

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

        // Notifikasi
        $lantaiNama = $ruangan->lantai->nama_lantai ?? 'Lantai tidak diketahui';

        \App\Models\Notification::create([
            'stafaset_id' => Auth::guard('stafaset')->id(),
            'message'     => 'Barang "' . $barang->nama_barang . '" diperbarui di ruangan ' . $ruangan->nama_ruangan . ' (' . $lantaiNama . ')',
            'type'        => 'barang_updated',
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
            'stafaset_id' => Auth::guard('stafaset')->id(),
            'message'     => 'Barang "' . $namaBarang . '" dihapus dari ruangan ' . $ruangan->nama_ruangan . ' (' . $lantaiNama . ')',
            'type'        => 'barang_deleted',
        ]);

        return redirect()
            ->route('ruangan.show', $ruanganId)
            ->with('success', 'Barang berhasil dihapus.');
    }

    public function importForm($ruangan_id)
    {
        $ruangan = Ruangan::with(['lantai', 'penanggungJawab'])->findOrFail($ruangan_id);

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
            'stafaset_id' => Auth::guard('stafaset')->id(),
            'message'     => 'Import barang ke ruangan ' . $ruangan->nama_ruangan . ' (' . $lantaiNama . ') berhasil dilakukan.',
            'type'        => 'barang_imported',
        ]);

        return redirect()
            ->route('ruangan.show', $ruangan_id)
            ->with('success', 'Import barang berhasil.');
    }
}