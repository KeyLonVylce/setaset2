<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StafAset;
use Illuminate\Support\Facades\Hash;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Auth;

class StafAsetController extends Controller
{
    /**
     * Menampilkan daftar semua staff (role = 'staff') dengan pagination 10 per halaman
     */
    public function index()
    {
        // Ambil semua data staff yang role-nya 'staff', urutkan descending berdasarkan created_at
        $staffs = StafAset::where('role', 'staff')->orderBy('created_at', 'desc')->paginate(10);
        return view('staff.index', compact('staffs'));
    }

    /**
     * Menampilkan form tambah staff
     */
    public function create()
    {
        return view('staff.create');
    }

    /**
     * Menyimpan data staff baru ke database
     * Role otomatis diisi 'staff', password di-hash
     */
    public function store(Request $request)
    {
        // Validasi input: username, nama, nip, email, password
        // NIP harus integer dan unik, email unik, password minimal 6 karakter
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:stafaset,username',
            'nama'     => 'required|string|max:150',
            'nip'      => 'required|integer|digits_between:1,20|unique:stafaset,nip',
            'email'    => 'required|email|unique:stafaset,email',
            'password' => 'required|string|min:6',
        ], [
            // Custom pesan error untuk setiap field
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'nama.required'     => 'Nama wajib diisi.',
            'nip.required'      => 'NIP wajib diisi.',
            'nip.integer'       => 'NIP harus berupa angka.',
            'nip.digits_between'=> 'NIP harus antara 1 hingga 20 digit.',
            'nip.unique'        => 'NIP sudah digunakan.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ], [
            // Nama atribut untuk tampilan error (opsional)
            'username' => 'Username',
            'nama'     => 'Nama',
            'nip'      => 'NIP',
            'email'    => 'Email',
            'password' => 'Password',
        ]);
    
        // Hash password sebelum disimpan
        $validated['password'] = Hash::make($validated['password']);
        // Tetapkan role sebagai staff
        $validated['role'] = 'staff';
    
        // Simpan ke database
        $staff = StafAset::create($validated);
    
        // Kirim notifikasi ke admin bahwa staff baru ditambahkan
        NotificationHelper::create(
            'staff',
            'tambah',
            "Akun staff <b>{$staff->nama}</b> ({$staff->username}) berhasil dibuat",
            'admin'
        );
    
        return redirect()->route('staff.index')
            ->with('success', 'Akun staff berhasil dibuat!');
    }

    /**
     * Menampilkan form edit staff
     */
    public function edit($id)
    {
        $staff = StafAset::findOrFail($id);
        return view('staff.edit', compact('staff'));
    }

    /**
     * Memperbarui data staff (hanya staff, bukan admin)
     * Admin tidak boleh diedit melalui form ini
     */
    public function update(Request $request, $id)
    {
        $staff = StafAset::findOrFail($id);

        // Cegah mengedit akun yang role-nya admin
        if ($staff->role === 'admin') {
            return back()->with('error', 'Tidak dapat mengedit akun Administrator!');
        }

        // Validasi input: username, nama, nip, email, dan password (nullable)
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:stafaset,username,' . $id,
            'nama'     => 'required|string|max:150',
            'nip'      => 'required|string|max:30|unique:stafaset,nip,' . $id,
            'email'    => 'required|email|unique:stafaset,email,' . $id,
            'password' => 'nullable|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'nama.required'     => 'Nama wajib diisi.',
            'nip.required'      => 'NIP wajib diisi.',
            'nip.unique'        => 'NIP sudah digunakan.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan.',
            'password.min'      => 'Password minimal 6 karakter.',
        ], [
            'username' => 'Username',
            'nama'     => 'Nama',
            'nip'      => 'NIP',
            'email'    => 'Email',
            'password' => 'Password',
        ]);

        // Jika password diisi, hash dan simpan; jika tidak, hapus dari array update
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $staff->update($validated);

        // Notifikasi ke admin bahwa staff diubah
        NotificationHelper::create(
            'staff',
            'edit',
            "Akun staff <b>{$staff->nama}</b> ({$staff->username}) diubah",
            'admin'
        );

        return redirect()->route('staff.index')
            ->with('success', 'Akun staff berhasil diupdate!');
    }

    /**
     * Menghapus akun staff (hanya role staff, tidak bisa hapus admin)
     * Juga tidak bisa menghapus akun sendiri yang sedang login
     */
    public function destroy($id)
    {
        $staff = StafAset::findOrFail($id);

        // Cegah penghapusan admin
        if ($staff->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus akun Administrator!');
        }

        // Cegah penghapusan akun sendiri
        if ($staff->id === auth('stafaset')->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri!');
        }

        $nama = $staff->nama;
        $username = $staff->username;

        $staff->delete();

        // Notifikasi ke admin bahwa staff dihapus
        NotificationHelper::create(
            'staff',
            'hapus',
            "Akun staff <b>{$nama}</b> ({$username}) dihapus",
            'admin'
        );

        return redirect()->route('staff.index')
            ->with('success', 'Akun staff berhasil dihapus!');
    }

    /**
     * Menampilkan halaman edit profil untuk user yang sedang login
     */
    public function editProfile()
    {
        $staff = Auth::guard('stafaset')->user();
        return view('profile.edit', compact('staff'));
    }

    /**
     * Memproses update profil user yang sedang login
     * Termasuk validasi password lama jika ingin mengganti password
     */
    public function updateProfile(Request $request)
    {
        $staff = Auth::guard('stafaset')->user();
    
        // Validasi data profile (tanpa password_lama dulu)
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:stafaset,username,' . $staff->id,
            'nama'     => 'required|string|max:150',
            'nip'      => 'required|string|max:30|unique:stafaset,nip,' . $staff->id,
            'email'    => 'required|email|unique:stafaset,email,' . $staff->id,
            'password' => 'nullable|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'nama.required'     => 'Nama wajib diisi.',
            'nip.required'      => 'NIP wajib diisi.',
            'nip.unique'        => 'NIP sudah digunakan.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);
    
        // ========== VALIDASI PASSWORD LAMA ==========
        // Jika password baru diisi, maka wajib cek password lama
        if ($request->filled('password')) {
            // 1. Validasi password_lama harus ada
            $request->validate([
                'password_lama' => 'required|string',
            ], [
                'password_lama.required' => 'Kata sandi lama wajib diisi jika ingin mengganti password.',
            ]);
    
            // 2. Cek kecocokan password lama dengan hash di database
            if (!Hash::check($request->password_lama, $staff->password)) {
                return back()
                    ->withErrors(['password_lama' => 'Kata sandi lama tidak sesuai.'])
                    ->withInput();
            }
    
            // 3. Jika cocok, hash password baru
            $validated['password'] = Hash::make($request->password);
        } else {
            // Jika password baru tidak diisi, hapus key 'password' dari array agar tidak update
            unset($validated['password']);
        }
    
        // Hapus password_lama dari array validated (kolom ini tidak ada di tabel)
        unset($validated['password_lama']);
    
        // Update data staff
        $staff->update($validated);
    
        return redirect()->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}