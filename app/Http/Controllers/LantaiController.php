<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;

class LantaiController extends Controller
{
    public function show(Request $request, $id)
    {
        $lantai = Lantai::findOrFail($id);

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

        Notification::create([
            'type'        => 'lantai',
            'aksi'        => 'tambah',
            'pesan'       => "Lantai <b>{$lantai->nama_lantai}</b> ditambahkan",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

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

        $lantai->update($request->only([
            'nama_lantai',
            'urutan',
            'keterangan'
        ]));

        Notification::create([
            'type'        => 'lantai',
            'aksi'        => 'edit',
            'pesan'       => "Lantai <b>{$lantai->nama_lantai}</b> diubah",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Lantai berhasil diupdate!');
    }

    public function destroy($id)
    {
        $lantai = Lantai::findOrFail($id);

        if ($lantai->ruangans()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus lantai yang masih memiliki ruangan!');
        }

        $namaLantai = $lantai->nama_lantai;
        $lantai->delete();

        Notification::create([
            'type'        => 'lantai',
            'aksi'        => 'hapus',
            'pesan'       => "Lantai <b>{$namaLantai}</b> dihapus",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

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

        Notification::create([
            'type'        => 'ruangan',
            'aksi'        => 'tambah',
            'pesan'       => "Ruangan <b>{$ruangan->nama_ruangan}</b> ditambahkan di lantai <b>{$lantai->nama_lantai}</b>",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function updateRuangan(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $lantai = Lantai::findOrFail($ruangan->lantai_id);

        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
            'nip_penanggung_jawab' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string',
        ]);

        $ruangan->update([
            'nama_ruangan' => $request->nama_ruangan,
            'penanggung_jawab' => $request->penanggung_jawab,
            'nip_penanggung_jawab' => $request->nip_penanggung_jawab,
            'keterangan' => $request->keterangan,
        ]);

        Notification::create([
            'type'        => 'ruangan',
            'aksi'        => 'edit',
            'pesan'       => "Ruangan <b>{$ruangan->nama_ruangan}</b> di lantai <b>{$lantai->nama_lantai}</b> diubah",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Ruangan berhasil diupdate!');
    }

    public function deleteRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        if ($ruangan->barangs()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus ruangan yang masih memiliki barang!');
        }

        $namaRuangan = $ruangan->nama_ruangan;
        $lantaiNama  = $ruangan->lantai;

        $ruangan->delete();

        Notification::create([
            'type'        => 'ruangan',
            'aksi'        => 'hapus',
            'pesan'       => "Ruangan <b>{$namaRuangan}</b> dihapus dari lantai <b>{$lantaiNama}</b>",
            'target_role' => 'admin',
            'user_id'     => Auth::guard('stafaset')->id(),
        ]);

        return back()->with('success', 'Ruangan berhasil dihapus!');
    }
}