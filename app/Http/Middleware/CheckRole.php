<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek login
        if (!auth('stafaset')->check()) {
            return redirect()->route('login');
        }

        $user = auth('stafaset')->user();

        // Cek role
        if (!in_array($user->role, $roles)) {
            return redirect()->route('home')->with(
                'error',
                'Anda tidak memiliki akses ke halaman ini!'
            );
        }

        return $next($request);
    }
}