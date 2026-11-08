<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan role-nya admin
        // [FIX] Sejak RBAC, $user->role adalah model Role (bukan string) -> bandingkan slug-nya
        if (auth()->check() && in_array(optional(auth()->user()->role)->slug, ['admin', 'super_admin'], true)) {
            return $next($request);
        }

        // Jika bukan admin, lempar error 403 (Forbidden) atau redirect
        abort(403, 'Akses Ditolak. Halaman ini khusus Administrator.');
    }
}