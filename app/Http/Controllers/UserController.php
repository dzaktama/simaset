<?php

namespace App\Http\Controllers;

use App\Models\User; // Jangan lupa panggil Model User
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Fungsi untuk menampilkan halaman index (Daftar User)
    public function index()
    {
        return view('home', [
            'title' => 'Halaman Home',
            'users' => User::all() // Ambil data dari database
        ]);
    }

    public function create()
    {
        return view('create', [
            'title' => 'Tambah User Baru'
        ]);
    }

    // FUNGSI BARU UNTUK SIMPAN DATA
public function store(Request $request)
{
    // 1. Validasi dulu inputannya (Biar gak asal-asalan)
    $validatedData = $request->validate([
        'name' => 'required|max:255',
        'email' => 'required|email|unique:users', // Email gak boleh kembar
        'password' => 'required|min:5'
    ]);

    // 2. Enkripsi Password (Biar aman, jadi acak-acakan di database)
    $validatedData['password'] = bcrypt($validatedData['password']);

    // 3. Simpan ke Database
    User::create($validatedData);

    // 4. Kalau sukses, tendang balik ke Halaman Utama
    return redirect('/');
}

// FUNGSI UNTUK MENGHAPUS DATA
public function destroy($id)
{
    // 1. Cari user berdasarkan ID
    $user = User::find($id);

    // 2. Hapus datanya
    $user->delete();

    // 3. Kembali ke halaman utama
    return redirect('/');
}

// FUNGSI TAMPILKAN FORM EDIT
public function edit($id)
{
    // Cari user berdasarkan ID, kalau gak ketemu error 404
    $user = User::findOrFail($id);

    return view('edit', [
        'title' => 'Edit Data User',
        'user' => $user // Kirim data user ke view edit
    ]);
}

// FUNGSI PROSES UPDATE
public function update(Request $request, $id)
{
    // 1. Cari user yang mau diedit
    $user = User::findOrFail($id);

    // 2. Validasi inputan
    $rules = [
        'name' => 'required|max:255',
        'email' => 'required|email', // Hapus unique biar gak error kalau email gak diganti
    ];

    // Cek kalau password diisi, berarti mau ganti password
    if($request->filled('password')) {
        $rules['password'] = 'min:5';
    }

    $validatedData = $request->validate($rules);

    // 3. Update Password kalau diisi
    if($request->filled('password')) {
        $validatedData['password'] = bcrypt($request->password);
    } else {
        // Kalau kosong, hapus key password dari array biar gak ikut ke-update jadi kosong
        unset($validatedData['password']);
    }

    // 4. Update ke Database
    $user->update($validatedData);

    // 5. Balik ke Home
    return redirect('/');
}
}