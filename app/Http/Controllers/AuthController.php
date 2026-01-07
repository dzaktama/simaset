<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman Login
     */
    public function login()
    {
        return view('auth.login', [
            'title' => 'Login'
        ]);
    }

    /**
     * Proses Login
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/'); // Redirect ke dashboard
        }

        return back()->with('loginError', 'Login gagal! Email atau password salah.');
    }

    /**
     * Tampilkan halaman Register
     */
    public function register()
    {
        return view('auth.register', [
            'title' => 'Register'
        ]);
    }

    /**
     * Proses Register User Baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:5|max:255'
        ]);

        // Enkripsi password
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        // Set default role (jika tidak diinput, otomatis jadi 'user')
        $validatedData['role'] = 'user'; 

        User::create($validatedData);

        // Opsional: Langsung login setelah register
        // $request->session()->flash('success', 'Registrasi berhasil! Silakan login.');
        
        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}