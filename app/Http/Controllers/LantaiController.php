<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;

class LantaiController extends Controller
{
    public function show($lantai)
    {
        $ruangans = Ruangan::where('lantai', $lantai)
            ->withCount('barangs')
            ->get();

        return view('lantai.show', compact('lantai', 'ruangans'));
    }

    public function storeRuangan(Request $request, $lantai)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
            'nip_penanggung_jawab' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string',
        ]);

        Ruangan::create([
            'nama_ruangan' => $request->nama_ruangan,
            'lantai' => $lantai,
            'penanggung_jawab' => $request->penanggung_jawab,
            'nip_penanggung_jawab' => $request->nip_penanggung_jawab,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function deleteRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return back()->with('success', 'Ruangan berhasil dihapus!');
    }
}
