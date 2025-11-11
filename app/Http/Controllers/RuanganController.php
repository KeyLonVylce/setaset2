<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Barang;

class RuanganController extends Controller
{
    public function show($id)
    {
        $ruangan = Ruangan::with('barangs')->findOrFail($id);
        
        return view('ruangan.show', compact('ruangan'));
    }

    public function export($id)
    {
        $ruangan = Ruangan::with('barangs')->findOrFail($id);
        
        return view('ruangan.export', compact('ruangan'));
    }
}