<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'title' => 'Manajemen User',
            'users' => User::latest()->get()
        ]);
    }

    public function create()
    {
        return view('users.create', ['title' => 'Tambah User Baru']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users',
            'role' => 'required',
            'password' => 'required|min:5|max:255'
        ]);

        // HASH MANUAL DISINI
        $validatedData['password'] = bcrypt($validatedData['password']); 

        User::create($validatedData); // Simpan

        return redirect('/users')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'title' => 'Edit Data User',
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|max:255',
            'role' => 'required'
        ];

        if($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }

        $validatedData = $request->validate($rules);

        if($request->password) {
            // HASH MANUAL DISINI
            $validatedData['password'] = bcrypt($request->password);
        }

        $user->update($validatedData);

        return redirect('/users')->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id == auth()->user()->id) {
            return back()->with('loginError', 'Anda tidak bisa menghapus akun sendiri!');
        }
        $user->delete();
        return redirect('/users')->with('success', 'User telah dihapus.');
    }
}