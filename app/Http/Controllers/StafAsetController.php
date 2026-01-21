<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StafAset;
use Illuminate\Support\Facades\Hash;
use App\Helpers\NotificationHelper;

class StafAsetController extends Controller
{
    public function index()
    {
        $staffs = StafAset::orderBy('created_at', 'desc')->paginate(10);
        return view('staff.index', compact('staffs'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:stafaset,username',
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:30|unique:stafaset,nip',
            'password' => 'required|string|min:6',
            'can_edit' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'staff'; // Selalu staff
        $validated['can_edit'] = $request->has('can_edit') ? true : false;

        $staff = StafAset::create($validated);

        NotificationHelper::create(
            'staff',
            'tambah',
            "Akun staff <b>{$staff->nama}</b> ({$staff->username}) berhasil dibuat"
        );

        return redirect()->route('staff.index')
            ->with('success', 'Akun staff berhasil dibuat!');
    }

    public function edit($id)
    {
        $staff = StafAset::findOrFail($id);
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = StafAset::findOrFail($id);

        // Cegah edit akun admin
        if ($staff->role === 'admin') {
            return back()->with('error', 'Tidak dapat mengedit akun Administrator!');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:stafaset,username,' . $id,
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:30|unique:stafaset,nip,' . $id,
            'password' => 'nullable|string|min:6',
            'can_edit' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['can_edit'] = $request->has('can_edit') ? true : false;

        $staff->update($validated);

        NotificationHelper::create(
            'staff',
            'edit',
            "Akun staff <b>{$staff->nama}</b> ({$staff->username}) diubah"
        );

        return redirect()->route('staff.index')
            ->with('success', 'Akun staff berhasil diupdate!');
    }

    public function destroy($id)
    {
        $staff = StafAset::findOrFail($id);

        // Cegah hapus akun admin
        if ($staff->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus akun Administrator!');
        }

        // Cegah admin menghapus akun sendiri
        if ($staff->id === auth('stafaset')->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri!');
        }

        $nama = $staff->nama;
        $username = $staff->username;

        $staff->delete();

        NotificationHelper::create(
            'staff',
            'hapus',
            "Akun staff <b>{$nama}</b> ({$username}) dihapus"
        );

        return redirect()->route('staff.index')
            ->with('success', 'Akun staff berhasil dihapus!');
    }
}