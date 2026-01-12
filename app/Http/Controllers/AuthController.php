<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login', [ 
            'title' => 'Login',
            'active' => 'login'
        ]);
    }

    public function authenticate(Request $request)
    {
        // [IMPROVISASI] Validasi Captcha
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|captcha'
        ], [
            // Custom Error Message biar lebih ramah
            'captcha.required' => 'Kode keamanan wajib diisi.',
            'captcha.captcha' => 'Kode keamanan salah! Silakan coba lagi.'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Kalau password salah, input email jangan dihilangkan (UX)
        return back()->with('loginError', 'Login gagal! Email atau password salah.')->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
    
    // [FEATURE] Fungsi Refresh Captcha via AJAX
    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img('flat')]);
    }
}