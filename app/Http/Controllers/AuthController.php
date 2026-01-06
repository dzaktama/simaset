<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. TAMPILKAN FORM LOGIN
    public function showLogin()
    {
        return view('auth.login', ['title' => 'Login']);
    }

    // 2. PROSES LOGIN
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/'); // Sukses -> Masuk Home
        }

        // Gagal -> Balik ke login bawa pesan error
        return back()->with('loginError', 'Login Gagal! Cek email atau password.');
    }

    // 3. TAMPILKAN FORM REGISTER
    public function showRegister()
    {
        return view('auth.register', ['title' => 'Register']);
    }

    // 4. PROSES REGISTER
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|max:255'
        ]);

        // Enkripsi password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Simpan User
        User::create($validatedData);

        // Langsung arahkan ke login
        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // 5. PROSES LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}