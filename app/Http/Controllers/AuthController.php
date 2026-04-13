<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\StafAset;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     * Jika staf aset sudah login, redirect ke halaman home.
     */
    public function showLogin()
    {
        // Cek apakah pengguna sudah login menggunakan guard 'stafaset'
        if (Auth::guard('stafaset')->check()) {
            return redirect()->route('home');
        }
        // Tampilkan view auth.login
        return view('auth.login');
    }

    /**
     * Memproses request login dari form.
     */
    public function login(Request $request)
    {
        // Validasi input username dan password harus diisi
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cari staf berdasarkan username
        $staf = StafAset::where('username', $request->username)->first();

        // Jika staf ditemukan dan password cocok
        if ($staf && Hash::check($request->password, $staf->password)) {
            // Login menggunakan guard 'stafaset', dengan remember token jika dicentang
            Auth::guard('stafaset')->login($staf, $request->filled('remember'));
            
            // Regenerasi session untuk keamanan (mencegah session fixation)
            $request->session()->regenerate();
            
            // Redirect ke halaman home dengan pesan sukses
            return redirect()->route('home')->with('success', 'Selamat datang, ' . $staf->nama . '!');
        }

        // Jika login gagal, kembali ke halaman sebelumnya dengan error
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    /**
     * Melakukan logout pengguna dari guard 'stafaset'.
     */
    public function logout(Request $request)
    {
        // Logout dari guard stafaset
        Auth::guard('stafaset')->logout();
        
        // Hapus session dan regenerate token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

    /**
     * Menampilkan form lupa password (input email).
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Memproses pengiriman link reset password ke email pengguna.
     */
    public function sendResetLink(Request $request)
    {
        // Validasi email harus diisi dan format email benar
        $request->validate([
            'email' => 'required|email'
        ]);
    
        // Cari user berdasarkan email di tabel stafaset
        $user = DB::table('stafaset')
            ->where('email', $request->email)
            ->first();
    
        // Jika email tidak terdaftar, kembali dengan pesan error
        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan!');
        }
    
        // Generate token random sepanjang 64 karakter
        $token = Str::random(64);
    
        // 🔥 MATIKAN TOKEN LAMA
        // Hapus token reset dan expired_at yang lama untuk email ini
        DB::table('stafaset')
            ->where('email', $request->email)
            ->update([
                'reset_token' => null,
                'reset_token_expired_at' => null
            ]);
    
        // 🔥 SIMPAN TOKEN BARU
        // Simpan token baru beserta waktu kadaluarsa (60 menit)
        DB::table('stafaset')
            ->where('email', $request->email)
            ->update([
                'reset_token' => $token,
                'reset_token_expired_at' => now()->addMinutes(60)
            ]);
    
        // Buat link reset password yang akan dikirim ke email
        $link = url('/reset-password/' . $token);
    
        // Kirim email menggunakan view 'emails.reset-password' dengan data link
        Mail::send('emails.reset-password', ['link' => $link], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });
    
        // Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Link reset dikirim ke email!');
    }

    /**
     * Menampilkan form reset password berdasarkan token.
     * Memeriksa apakah token masih valid dan belum kadaluarsa.
     */
    public function showResetForm($token)
    {
        // Cari user berdasarkan reset_token yang tidak null dan expired_at > sekarang
        $user = DB::table('stafaset')
            ->where('reset_token', $token)
            ->whereNotNull('reset_token')
            ->where('reset_token_expired_at', '>', now())
            ->first();
    
        // Jika token tidak valid atau sudah kadaluarsa, redirect ke login dengan error
        if (!$user) {
            return redirect('/login')->with('error', 'Token sudah kadaluarsa atau tidak valid!');
        }
    
        // Tampilkan form reset password, kirimkan token ke view
        return view('auth.reset-password', compact('token'));
    }

    /**
     * Memproses reset password (update password baru ke database).
     */
    public function resetPassword(Request $request, $token)
    {
        // Validasi password baru minimal 6 karakter dan konfirmasi password harus sama
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);
    
        // Cek kembali validitas token (harus ada dan belum kadaluarsa)
        $user = DB::table('stafaset')
            ->where('reset_token', $token)
            ->whereNotNull('reset_token')
            ->where('reset_token_expired_at', '>', now())
            ->first();
    
        // Jika token tidak valid, redirect ke login dengan error
        if (!$user) {
            return redirect('/login')->with('error', 'Token tidak valid atau sudah digunakan!');
        }
    
        // Update password user dengan hash baru, dan hapus token reset agar tidak bisa dipakai lagi
        DB::table('stafaset')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($request->password),
    
                // 🔥 HAPUS TOKEN BIAR TIDAK BISA DIPAKAI LAGI
                'reset_token' => null,
                'reset_token_expired_at' => null
            ]);
    
        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Password berhasil direset!');
    }
}