<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
}
