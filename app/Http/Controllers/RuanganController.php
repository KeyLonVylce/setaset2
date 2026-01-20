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

    public function export(Request $request, $id)
    {
        $ruangan = Ruangan::with('barangs')->findOrFail($id);
        
        // Cek apakah request ingin export ke Excel
        if ($request->has('format') && $request->format === 'excel') {
            $filename = 'Kartu_Inventaris_' . str_replace(' ', '_', $ruangan->nama_ruangan) . '_' . date('Y-m-d') . '.xlsx';
            return Excel::download(new KartuInventarisExport($ruangan), $filename);
        }
        
        // Default: tampilkan view untuk print/PDF
        return view('ruangan.export', compact('ruangan'));
    }
}