<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth('stafaset')->check()) {
            return redirect()->route('login');
        }

        $user = auth('stafaset')->user();
        
        if ($user->role !== $role) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman ini. Hanya ' . strtoupper($role) . ' yang diizinkan.');
        }

        return $next($request);
    }
}