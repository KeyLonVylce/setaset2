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
        
        // Ambil ruangan
        $ruangan = Ruangan::findOrFail($id);
        
        // Query barang dengan search dan pagination
        $barangsQuery = Barang::where('ruangan_id', $id);
        
        if ($search) {
            $barangsQuery->where(function($query) use ($search) {
                $query->where('nama_barang', 'LIKE', "%$search%")
                    ->orWhere('kode_barang', 'LIKE', "%$search%")
                    ->orWhere('merk_model', 'LIKE', "%$search%")
                    ->orWhere('keterangan', 'LIKE', "%$search%");
            });
        }
        
        // Pagination dengan 5 barang per halaman
        $barangs = $barangsQuery->paginate(10)->withQueryString();
        
        return view('ruangan.show', compact('ruangan', 'barangs'));
    }
    
    public function export($id)
    {
        $ruangan = Ruangan::with('barangs')->findOrFail($id);
        
        return view('ruangan.export', compact('ruangan'));
    }
}