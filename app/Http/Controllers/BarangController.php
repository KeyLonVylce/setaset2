<?php

namespace App\Http\Controllers;

use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Ruangan;

class BarangController extends Controller
{
    public function create($ruangan_id)
    {
        $ruangan = Ruangan::findOrFail($ruangan_id);
        return view('barang.create', compact('ruangan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'no_urut' => 'nullable|integer',
            'nama_barang' => 'required|string|max:150',
            'merk_model' => 'nullable|string|max:150',
            'no_seri_pabrik' => 'nullable|string|max:100',
            'ukuran' => 'nullable|string|max:50',
            'bahan' => 'nullable|string|max:100',
            'tahun_pembuatan' => 'nullable|integer|min:1900|max:' . date('Y'),
            'kode_barang' => 'nullable|string|max:50',
            'jumlah' => 'required|integer|min:1',
            'harga_perolehan' => 'nullable|string|max:255',
            'kondisi' => 'required|in:B,KB,RB',
            'keterangan' => 'nullable|string',
        ]);

        Barang::create($validated);

        return redirect()->route('ruangan.show', $validated['ruangan_id'])
            ->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $barang = Barang::with('ruangan')->findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no_urut' => 'nullable|integer',
            'nama_barang' => 'required|string|max:150',
            'merk_model' => 'nullable|string|max:150',
            'no_seri_pabrik' => 'nullable|string|max:100',
            'ukuran' => 'nullable|string|max:50',
            'bahan' => 'nullable|string|max:100',
            'tahun_pembuatan' => 'nullable|integer|min:1900|max:' . date('Y'),
            'kode_barang' => 'nullable|string|max:50',
            'jumlah' => 'required|integer|min:0',
            'harga_perolehan' => 'nullable|string|max:255',
            'kondisi' => 'required|in:B,KB,RB',
            'keterangan' => 'nullable|string',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($validated);

        return redirect()->route('ruangan.show', $barang->ruangan_id)
            ->with('success', 'Barang berhasil diupdate!');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $ruangan_id = $barang->ruangan_id;
        $barang->delete();

        return redirect()->route('ruangan.show', $ruangan_id)
            ->with('success', 'Barang berhasil dihapus!');
    }

    public function importForm($ruangan_id)
    {
        $ruangan = Ruangan::findOrFail($ruangan_id);
        return view('barang.import', compact('ruangan'));
    }

    public function import(Request $request, $ruangan_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new BarangImport($ruangan_id), $request->file('file'));

        return redirect()->route('barang.import.form', $ruangan_id)
            ->with('success', 'Data barang berhasil diimport!');
    }
}
