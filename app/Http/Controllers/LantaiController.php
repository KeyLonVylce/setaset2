<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lantai;
use App\Models\Ruangan;
use App\Helpers\NotificationHelper;

class LantaiController extends Controller
{
    public function show(Request $request, $id)
    {
        // Ambil lantai
        $lantai = Lantai::findOrFail($id);

        // Query ruangan + jumlah barang
        $ruangans = $lantai->ruangans()
            ->withCount('barangs')
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_ruangan', 'like', "%{$search}%")
                      ->orWhere('penanggung_jawab', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->paginate(3)
            ->withQueryString();

        return view('lantai.show', compact('lantai', 'ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai',
            'urutan' => 'nullable|integer',
            'keterangan' => 'nullable|string',
        ]);

        $urutan = $request->urutan ?? (Lantai::max('urutan') ?? 0) + 1;

        $lantai = Lantai::create([
            'nama_lantai' => $request->nama_lantai,
            'urutan' => $urutan,
            'keterangan' => $request->keterangan,
        ]);

        NotificationHelper::create(
            'lantai',
            'tambah',
            "Lantai <b>{$lantai->nama_lantai}</b> ditambahkan"
        );

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

        $nama = $lantai->nama_lantai;

        $lantai->update($request->only([
            'nama_lantai',
            'urutan',
            'keterangan'
        ]));

        NotificationHelper::create(
            'lantai',
            'edit',
            "Lantai <b>{$nama}</b> diubah"
        );

        return back()->with('success', 'Lantai berhasil diupdate!');
    }

    public function destroy($id)
    {
        $lantai = Lantai::findOrFail($id);

        if ($lantai->ruangans()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus lantai yang masih memiliki ruangan!');
        }

        $nama = $lantai->nama_lantai;
        $lantai->delete();

        NotificationHelper::create(
            'lantai',
            'hapus',
            "Lantai <b>{$nama}</b> dihapus"
        );

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

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai_id,
            'nama_ruangan' => $request->nama_ruangan,
            'lantai' => $lantai->nama_lantai,
            'penanggung_jawab' => $request->penanggung_jawab,
            'nip_penanggung_jawab' => $request->nip_penanggung_jawab,
            'keterangan' => $request->keterangan,
        ]);

        NotificationHelper::create(
            'ruangan',
            'tambah',
            "Ruangan <b>{$ruangan->nama_ruangan}</b> ditambahkan di lantai <b>{$lantai->nama_lantai}</b>"
        );

        return back()->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function deleteRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        if ($ruangan->barangs()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus ruangan yang masih memiliki barang!');
        }

        $nama = $ruangan->nama_ruangan;
        $lantai = $ruangan->lantai;

        $ruangan->delete();

        NotificationHelper::create(
            'ruangan',
            'hapus',
            "Ruangan <b>{$nama}</b> di lantai <b>{$lantai}</b> dihapus"
        );

        return back()->with('success', 'Ruangan berhasil dihapus!');
    }
}
