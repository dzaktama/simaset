<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek User Login & Role
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'AKSES DITOLAK: Halaman ini khusus Admin.');
        }

        return $next($request);
    }
}