<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // TAMBAHKAN INI!
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Ambil role user yang sedang login
        $userRole = Auth::user()->role;

        // Cek apakah role user ada dalam daftar role yang diizinkan
        if (!in_array($userRole, $roles)) {
            // Jika tidak, redirect ke dashboard dengan pesan error
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}