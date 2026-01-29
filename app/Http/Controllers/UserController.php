<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Constructor Controller
     * Menerapkan Middleware Hak Akses (Authorization) sebelum fungsi dijalankan.
     */
    public function __construct()
    {
        // Hanya user dengan permission 'user.view' yang boleh lihat daftar user
        $this->middleware('can:user.view')->only(['index', 'show']);
        
        // Hanya yang punya 'user.create' boleh buka form tambah user & simpan
        $this->middleware('can:user.create')->only(['create', 'store']);
        
        // Hanya yang punya 'user.edit' boleh edit data user
        $this->middleware('can:user.edit')->only(['edit', 'update']);
        
        // Hanya yang punya 'user.delete' boleh hapus user
        $this->middleware('can:user.delete')->only(['destroy']);
    }

    /**
     * Menampilkan daftar semua user.
     */
    public function index()
    {
        // Ambil data user beserta aset yang sedang dipinjam (status 'deployed')
        $query = User::with(['assets' => function($q) {
            $q->where('status', 'deployed');
        }])->latest();

        // Fitur Pencarian (Search)
        // Mencari berdasarkan nama, email, NIP, HP, atau jabatan
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('employee_id', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('department', 'like', '%' . $search . '%')
                  ->orWhere('position', 'like', '%' . $search . '%');
            });
        }

        // Fitur Filter Role (Saring berdasarkan jabatan)
        if (request('role')) {
            $query->where('role', request('role'));
        }

        return view('users.index', [
            'title' => 'Manajemen Pengguna',
            'users' => $query->get()
        ]);
    }

    /**
     * Menampilkan form tambah user baru.
     */
    public function create()
    {
        return view('users.create', ['title' => 'Tambah User']);
    }

    /**
     * Menyimpan data user baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Basic Validation)
        // Employee ID tidak divalidasi disini karena akan digenerate otomatis
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:5',
            'role' => 'required|in:admin,user,super_admin,service_center',
            'phone' => 'nullable|max:15',
            'department' => 'required|max:100',
            'position' => 'required|max:100',
            'permissions' => 'nullable|array' // Pastikan permissions bentuknya Array
        ]);

        // 2. Auto Generate Employee ID (NIP Otomatis)
        $validatedData['employee_id'] = $this->generateEmployeeId();

        // 3. Hash Password (Amankan password) & Buat User
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        // Simpan ke database (Kolom permissions otomatis jadi JSON berkat 'casts' di Model)
        $user = User::create($validatedData);

        return redirect('/users')->with('success', 'User berhasil ditambahkan! NIP Otomatis: ' . $user->employee_id);
    }

    /**
     * Menampilkan form edit user.
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'title' => 'Edit User',
            'user' => $user,
            // Kirim ID saran baru jika diperlukan (opsional)
            'suggestedId' => $this->generateEmployeeId() 
        ]);
    }

    /**
     * Memperbarui data user yang sudah ada.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|max:255',
            'role' => 'required|in:admin,user,super_admin,service_center',
            'phone' => 'nullable|max:15',
            'department' => 'required|max:100',
            'position' => 'required|max:100',
            'permissions' => 'nullable|array' // Validasi permissions
        ];

        // Validasi Email & NIP (hanya jika berubah) agar tidak error 'sudah terpakai'
        if($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }
        if($request->employee_id != $user->employee_id) {
            $rules['employee_id'] = 'required|unique:users|max:20';
        }

        $validatedData = $request->validate($rules);

        // Jika password diisi, hash password baru. Jika kosong, biarkan password lama.
        if($request->password) {
            $validatedData['password'] = Hash::make($request->password);
        }

        // Jika permissions tidak dikirim (semua checkbox kosong), set array kosong
        if (!isset($validatedData['permissions'])) {
            $validatedData['permissions'] = [];
        }

        $user->update($validatedData);
        return redirect('/users')->with('success', 'Data user diperbarui!');
    }

    /**
     * Fungsi Helper: Membuat NIP Otomatis
     * Format: EMP-YYYYMM-XXX (Contoh: EMP-202401-001)
     */
    private function generateEmployeeId()
    {
        $prefix = 'EMP-' . date('Ym') . '-';
        
        // Cari user terakhir yang punya prefix bulan ini
        $lastUser = User::where('employee_id', 'like', $prefix . '%')
                        ->orderBy('employee_id', 'desc')
                        ->first();
        
        if ($lastUser) {
            // Ambil 3 digit terakhir, ubah jadi integer, tambah 1
            $lastNumber = intval(substr($lastUser->employee_id, -3));
            $newNumber = $lastNumber + 1;
        } else {
            // Jika belum ada user bulan ini, mulai dari 1
            $newNumber = 1;
        }
        
        // Pad dengan nol di depan biar jadi 3 digit (001, 002, dst)
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Menghapus user dari sistem.
     */
    public function destroy(User $user)
    {
        // Mencegah user menghapus dirinya sendiri
        if(auth()->id() == $user->id) {
            return back()->with('loginError', 'Tidak bisa menghapus akun sendiri!');
        }
        
        $user->delete();
        return redirect('/users')->with('success', 'User dihapus.');
    }
}