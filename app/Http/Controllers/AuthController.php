<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'captcha' => 'required|captcha' 
        ], [
            // Custom Error Message
            'captcha.required' => 'Kode keamanan wajib diisi.',
            'captcha.captcha' => 'Kode keamanan salah! Silakan coba lagi.'
        ]);

        $credentials = $request->only('email', 'password');

        $remember = $request->boolean('remember-me');

        if (Auth::attempt($credentials, $remember)) {
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
        // Hapus session impersonate jika ada
        $request->session()->forget('impersonator_id');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    /**
     * Mode Override: Login sebagai user lain (Impersonation)
     * Diakses oleh Super Admin
     */
    public function impersonate($userId)
    {
        $originalUser = Auth::user();

        // Security Check: Hanya Real Super Admin yang boleh
        // Cek juga session 'impersonator_id' untuk mencegah nested impersonation atau override dari user biasa
        // Namun, jika sedang impersonate, Auth::user() adalah user target.
        // Jadi kita harus cek apakah session 'impersonator_id' SUDAH ADA (berarti sedang impersonate)
        // ATAU role user saat ini adalah super_admin.
        
        $canImpersonate = $originalUser->role === 'super_admin' || session()->has('impersonator_id');

        if (!$canImpersonate) {
            abort(403, 'Unauthorized action.');
        }

        $userToImpersonate = User::findOrFail($userId);

        // Mencegah impersonate diri sendiri (loop)
        if ($userToImpersonate->id === $originalUser->id && !session()->has('impersonator_id')) {
             return back()->with('error', 'Anda sudah login sebagai akun ini.');
        }

        // Jika belum ada session impersonator, simpan ID admin asli
        if (!session()->has('impersonator_id')) {
            session(['impersonator_id' => $originalUser->id]);
        }

        // Login sebagai user target
        Auth::login($userToImpersonate);

        return redirect()->route('dashboard')->with('success', "Mode Override Aktif: Anda sekarang login sebagai {$userToImpersonate->name} ({$userToImpersonate->role})");
    }

    /**
     * Keluar dari Mode Override
     */
    public function leaveImpersonation()
    {
        if (session()->has('impersonator_id')) {
            $originalUserId = session('impersonator_id');
            
            // Login kembali ke user asli
            Auth::loginUsingId($originalUserId);
            
            // Hapus session
            session()->forget('impersonator_id');

            return redirect()->route('dashboard')->with('success', 'Mode Override Nonaktif: Welcome back, Super Admin!');
        }

        return redirect()->route('dashboard');
    }
    
    // [FEATURE] Fungsi Refresh Captcha via AJAX
    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img('flat')]);
    }
}