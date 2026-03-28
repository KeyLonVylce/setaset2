<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StafAset;
use Illuminate\Support\Facades\Hash;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Auth;

class StafAsetController extends Controller
{
    public function index()
    {
        $staffs = StafAset::where('role', 'staff')->orderBy('created_at', 'desc')->paginate(10);
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
            'email' => 'required|string|unique:stafaset,email',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'staff';

        $staff = StafAset::create($validated);

        NotificationHelper::create(
            'staff',
            'tambah',
            "Akun staff <b>{$staff->nama}</b> ({$staff->username}) berhasil dibuat",
            'admin'
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

        if ($staff->role === 'admin') {
            return back()->with('error', 'Tidak dapat mengedit akun Administrator!');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:stafaset,username,' . $id,
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:30|unique:stafaset,nip,' . $id,
            'email' => 'required|string|unique:stafaset,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $staff->update($validated);

        NotificationHelper::create(
            'staff',
            'edit',
            "Akun staff <b>{$staff->nama}</b> ({$staff->username}) diubah",
            'admin'
        );

        return redirect()->route('staff.index')
            ->with('success', 'Akun staff berhasil diupdate!');
    }

    public function destroy($id)
    {
        $staff = StafAset::findOrFail($id);

        if ($staff->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus akun Administrator!');
        }

        if ($staff->id === auth('stafaset')->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri!');
        }

        $nama = $staff->nama;
        $username = $staff->username;

        $staff->delete();

        NotificationHelper::create(
            'staff',
            'hapus',
            "Akun staff <b>{$nama}</b> ({$username}) dihapus",
            'admin'
        );

        return redirect()->route('staff.index')
            ->with('success', 'Akun staff berhasil dihapus!');
    }

    public function editProfile()
    {
        $staff = Auth::guard('stafaset')->user();
        return view('profile.edit', compact('staff'));
    }

    public function updateProfile(Request $request)
    {
        $staff = Auth::guard('stafaset')->user();

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:stafaset,username,' . $staff->id,
            'nama'     => 'required|string|max:150',
            'nip'      => 'required|string|max:30|unique:stafaset,nip,' . $staff->id,
            'email'    => 'required|email|unique:stafaset,email,' . $staff->id,
            'password' => 'nullable|string|min:6',
        ]);

        // Jika password diisi, hash dan set; jika tidak, hapus dari array
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $staff->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}