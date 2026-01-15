<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan Halaman Login (GET)
     * Di route: [AuthController::class, 'showLoginForm']
     */
    public function showLoginForm() 
    {
        return view('auth.login', [ 
            'title' => 'Login',
            'active' => 'login'
        ]);
    }

    /**
     * Memproses Login (POST)
     * Di route: [AuthController::class, 'login']
     */
    public function login(Request $request)
    {
        // [IMPROVISASI] Validasi Captcha
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            // 'captcha' => 'required|captcha' // Hidupkan jika sudah install package captcha
        ], [
            // Custom Error Message
            // 'captcha.required' => 'Kode keamanan wajib diisi.',
            // 'captcha.captcha' => 'Kode keamanan salah! Silakan coba lagi.'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // [PERBAIKAN] Redirect ke route 'dashboard' (URI: /home) untuk semua role.
            // Controller dashboard akan menangani tampilan berdasarkan role.
            return redirect()->intended(route('dashboard'));
        }

        // Kalau password salah, input email jangan dihilangkan (UX)
        return back()->with('loginError', 'Login gagal! Email atau password salah.')->withInput($request->only('email'));
    }

    /**
     * Logout User
     */
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