<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        // 1. Cek Mode Penyamaran (Impersonation)
        // Jika session 'impersonate_role' ada, gunakan itu sebagai role sementara
        $currentRole = session('impersonate_role', $user->role);

        // 2. Super Admin Bypass (Kecuali sedang menyamar)
        if ($user->role === 'super_admin' && !session('impersonate_role')) {
            return $next($request);
        }

        // 3. Cek apakah role user (atau role samaran) ada di daftar allowed roles
        if (in_array($currentRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak: Role Anda (' . $currentRole . ') tidak memiliki izin.');
    }
}
