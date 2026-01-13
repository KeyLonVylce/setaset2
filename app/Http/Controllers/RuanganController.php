<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Barang;

class RuanganController extends Controller
{
    public function show(Request $request, $id)
    {
    $search = $request->search;

    $ruangan = Ruangan::with(['barangs' => function ($query) use ($search) {
        if ($search) {
            $query->where('nama_barang', 'LIKE', "%$search%")
                ->orWhere('kode_barang', 'LIKE', "%$search%")
                ->orWhere('merk_model', 'LIKE', "%$search%")
                ->orWhere('keterangan', 'LIKE', "%$search%");
        }
    }])->findOrFail($id);

    return view('ruangan.show', compact('ruangan'));
    }

    public function export($id)
    {
        $ruangan = Ruangan::with('barangs')->findOrFail($id);
        
        return view('ruangan.export', compact('ruangan'));
    }
}