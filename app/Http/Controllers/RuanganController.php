<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RuanganController extends Controller
{
    public function show(Request $request, $id)
    {
        $ruangan = Ruangan::with(['lantai'])->findOrFail($id);

        $query = Barang::where('ruangan_id', $id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('merk_model', 'like', "%{$search}%")
                  ->orWhere('no_seri_pabrik', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $barangs = $query->paginate(20)->withQueryString();

        return view('ruangan.show', compact('ruangan', 'barangs'));
    }

    public function export($id)
    {
        $ruangan = Ruangan::with(['lantai', 'barangs'])->findOrFail($id);

        $pdf = Pdf::loadView('ruangan.export', compact('ruangan'))
                  ->setPaper('a4', 'landscape');

        $filename = 'kartu-inventaris-' . str_replace(' ', '-', strtolower($ruangan->nama_ruangan)) . '.pdf';

        return $pdf->download($filename);
    }
}