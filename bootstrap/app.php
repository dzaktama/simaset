<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // [PERBAIKAN] Ubah 'is_admin' menjadi 'admin' agar sesuai dengan routes/web.php
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            return redirect()->back()->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses fitur ini. Silakan hubungi Super Admin.');
        });
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            return redirect()->back()->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk melakukan tindakan ini.');
        });
    })
    ->create();