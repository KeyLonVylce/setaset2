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
    public function showLogin()
    {
        if (Auth::guard('stafaset')->check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $staf = StafAset::where('username', $request->username)->first();

        if ($staf && Hash::check($request->password, $staf->password)) {
            Auth::guard('stafaset')->login($staf, $request->filled('remember'));
            
            $request->session()->regenerate();
            
            return redirect()->route('home')->with('success', 'Selamat datang, ' . $staf->nama . '!');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::guard('stafaset')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
    
        $user = DB::table('stafaset')
            ->where('email', $request->email)
            ->first();
    
        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan!');
        }
    
        $token = Str::random(64);
    
        // 🔥 MATIKAN TOKEN LAMA
        DB::table('stafaset')
            ->where('email', $request->email)
            ->update([
                'reset_token' => null,
                'reset_token_expired_at' => null
            ]);
    
        // 🔥 SIMPAN TOKEN BARU
        DB::table('stafaset')
            ->where('email', $request->email)
            ->update([
                'reset_token' => $token,
                'reset_token_expired_at' => now()->addMinutes(60)
            ]);
    
        $link = url('/reset-password/' . $token);
    
        Mail::send('emails.reset-password', ['link' => $link], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });
    
        return back()->with('success', 'Link reset dikirim ke email!');
    }

    public function showResetForm($token)
    {
        $user = DB::table('stafaset')
            ->where('reset_token', $token)
            ->whereNotNull('reset_token')
            ->where('reset_token_expired_at', '>', now())
            ->first();
    
        if (!$user) {
            return redirect('/login')->with('error', 'Token sudah kadaluarsa atau tidak valid!');
        }
    
        return view('auth.reset-password', compact('token'));
    }

    public function resetPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);
    
        $user = DB::table('stafaset')
            ->where('reset_token', $token)
            ->whereNotNull('reset_token')
            ->where('reset_token_expired_at', '>', now())
            ->first();
    
        if (!$user) {
            return redirect('/login')->with('error', 'Token tidak valid atau sudah digunakan!');
        }
    
        DB::table('stafaset')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($request->password),
    
                // 🔥 HAPUS TOKEN BIAR TIDAK BISA DIPAKAI LAGI
                'reset_token' => null,
                'reset_token_expired_at' => null
            ]);
    
        return redirect('/login')->with('success', 'Password berhasil direset!');
    }
}
