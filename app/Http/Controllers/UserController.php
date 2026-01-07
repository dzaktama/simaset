<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user
     */
    public function index()
    {
        return view('users.index', [
            'title' => 'Manajemen User',
            'users' => User::latest()->get()
        ]);
    }

    /**
     * Form tambah user
     */
    public function create()
    {
        return view('users.create', [
            'title' => 'Tambah User Baru'
        ]);
    }

    /**
     * Simpan user baru ke database
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users',
            'role' => 'required|in:admin,user',
            'password' => 'required|min:5|max:255'
        ]);

        // Enkripsi password
        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return redirect('/users')->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Form edit user
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'title' => 'Edit Data User',
            'user' => $user
        ]);
    }

    /**
     * Update data user
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|max:255',
            'role' => 'required|in:admin,user'
        ];

        // Cek jika email berubah (agar tidak error unique)
        if($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }

        $validatedData = $request->validate($rules);

        // Cek jika password diisi (artinya mau ganti password)
        if($request->password) {
            $validatedData['password'] = Hash::make($request->password);
        }

        $user->update($validatedData);

        return redirect('/users')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        // Proteksi: Admin tidak boleh menghapus akunnya sendiri yang sedang login
        if ($user->id == auth()->user()->id) {
            return back()->with('loginError', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        // Cek apakah user memegang aset (Opsional, tapi bagus untuk integritas data)
        // if ($user->assets()->count() > 0) {
        //     return back()->with('loginError', 'User ini masih memegang aset. Harap tarik aset terlebih dahulu.');
        // }

        $user->delete();
        return redirect('/users')->with('success', 'User telah dihapus.');
    }
}