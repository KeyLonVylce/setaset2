<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Barang;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KartuInventarisExport;

class RuanganController extends Controller
{
    public function show(Request $request, $id)
    {
        $search = $request->search;

        // Ambil data ruangan
        $ruangan = Ruangan::findOrFail($id);

        // Query barang berdasarkan ruangan
        $barangs = Barang::where('ruangan_id', $id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'LIKE', "%{$search}%")
                      ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                      ->orWhere('merk_model', 'LIKE', "%{$search}%")
                      ->orWhere('keterangan', 'LIKE', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('ruangan.show', compact('ruangan', 'barangs'));
    }

    public function export(Request $request, $id)
    {
        $ruangan = Ruangan::with('barangs')->findOrFail($id);

        // Export Excel
        if ($request->format === 'excel') {
            $filename = 'Kartu_Inventaris_' 
                . str_replace(' ', '_', $ruangan->nama_ruangan) 
                . '_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new KartuInventarisExport($ruangan),
                $filename
            );
        }

        // View print / PDF
        return view('ruangan.export', compact('ruangan'));
    }
}
