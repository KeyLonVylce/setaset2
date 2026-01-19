<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lantai;
use App\Models\Ruangan;

class LantaiController extends Controller
{
    public function show(Request $request, $id)
    {
        $lantai = Lantai::findOrFail($id);
        
        // Query ruangan dengan search dan pagination
        $query = $lantai->ruangans()->withCount('barangs');
        
        // Filter search jika ada
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_ruangan', 'like', '%' . $search . '%')
                  ->orWhere('penanggung_jawab', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }
        
        // Pagination dengan 4 ruangan per halaman
        $ruangans = $query->paginate(3)->withQueryString();

        return view('lantai.show', compact('lantai', 'ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai',
            'urutan' => 'nullable|integer',
            'keterangan' => 'nullable|string',
        ]);

        if (!$request->urutan) {
            $maxUrutan = Lantai::max('urutan') ?? 0;
            $request->merge(['urutan' => $maxUrutan + 1]);
        }

        Lantai::create($request->only(['nama_lantai', 'urutan', 'keterangan']));

        return back()->with('success', 'Lantai berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $lantai = Lantai::findOrFail($id);

        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai,' . $id,
            'urutan' => 'nullable|integer',
            'keterangan' => 'nullable|string',
        ]);

        $lantai->update($request->only(['nama_lantai', 'urutan', 'keterangan']));

        return back()->with('success', 'Lantai berhasil diupdate!');
    }

    public function destroy($id)
    {
        $lantai = Lantai::findOrFail($id);
        
        if ($lantai->ruangans()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus lantai yang masih memiliki ruangan!');
        }

        $lantai->delete();

        return redirect()->route('home')->with('success', 'Lantai berhasil dihapus!');
    }

    public function storeRuangan(Request $request, $lantai_id)
    {
        $lantai = Lantai::findOrFail($lantai_id);

        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
            'nip_penanggung_jawab' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string',
        ]);

        Ruangan::create([
            'lantai_id' => $lantai_id,
            'nama_ruangan' => $request->nama_ruangan,
            'lantai' => $lantai->nama_lantai,
            'penanggung_jawab' => $request->penanggung_jawab,
            'nip_penanggung_jawab' => $request->nip_penanggung_jawab,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function deleteRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        
        if ($ruangan->barangs()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus ruangan yang masih memiliki barang!');
        }

        $ruangan->delete();

        return back()->with('success', 'Ruangan berhasil dihapus!');
    }
}