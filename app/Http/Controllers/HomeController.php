<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lantai;

class HomeController extends Controller
{
    public function index()
    {
        $lantais = Lantai::withCount('ruangans')
            ->ordered()
            ->get();

        return view('home', compact('lantais'));
    }

    public function storeLantai(Request $request)
    {
        $request->validate([
            'nama_lantai' => 'required|string|max:50|unique:lantais,nama_lantai',
            'urutan' => 'nullable|integer',
            'keterangan' => 'nullable|string',
        ]);

        $urutan = $request->urutan ?? (Lantai::max('urutan') ?? 0) + 1;

        Lantai::create([
            'nama_lantai' => $request->nama_lantai,
            'urutan' => $urutan,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Lantai berhasil ditambahkan!');
    }
}