<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login', [ // Pastikan file view ada di resources/views/auth/login.blade.php
            'title' => 'Login',
            'active' => 'login'
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Cek role untuk redirect yang tepat (Opsional, dashboard aman untuk semua)
            return redirect()->intended('/dashboard');
        }

        return back()->with('loginError', 'Login gagal! Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // FIX: Redirect ke '/login' bukan '/'
        return redirect('/login');
    }
}