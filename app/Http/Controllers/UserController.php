<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        return view('users.create', ['title' => 'Tambah User']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users',
            'role' => 'required|in:admin,user',
            'password' => 'required|min:5'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        User::create($validatedData);

        return redirect('/users')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'title' => 'Edit User',
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|max:255',
            'role' => 'required|in:admin,user'
        ];

        if($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }

        $validatedData = $request->validate($rules);

        if($request->password) {
            $validatedData['password'] = Hash::make($request->password);
        }

        $user->update($validatedData);
        return redirect('/users')->with('success', 'Data user diperbarui!');
    }

    public function destroy(User $user)
    {
        if(auth()->id() == $user->id) {
            return back()->with('loginError', 'Tidak bisa menghapus akun sendiri!');
        }
        $user->delete();
        return redirect('/users')->with('success', 'User dihapus.');
    }
}