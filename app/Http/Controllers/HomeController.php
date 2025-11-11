<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua lantai yang unik
        $lantaiList = Ruangan::select('lantai')
            ->distinct()
            ->orderBy('lantai')
            ->pluck('lantai')
            ->filter()
            ->values();

        return view('home', compact('lantaiList'));
    }

    public function storeLantai(Request $request)
    {
        $request->validate([
            'nama_lantai' => 'required|string|max:20',
        ]);

        // Cek apakah lantai sudah ada
        $exists = Ruangan::where('lantai', $request->nama_lantai)->exists();
        
        if ($exists) {
            return back()->with('error', 'Lantai sudah ada!');
        }

        return back()->with('success', 'Lantai berhasil ditambahkan! Silakan tambahkan ruangan.');
    }
}
