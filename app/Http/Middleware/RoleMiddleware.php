<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = strtolower(Auth::user()->role);
        $requiredRole = strtolower($role);

        if ($userRole !== $requiredRole) {
            // Kalau role tidak sesuai, arahkan ke dashboard yang benar
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'cashier') {
                return redirect()->route('kasir.dashboard');
            } else {
                abort(403, 'Akses ditolak.');
            }
        }

        return $next($request);
    }
}
