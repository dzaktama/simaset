<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Konstruktor untuk proteksi route.
     * Hanya bisa diakses jika punya permission user.view.
     */
    public function __construct()
    {
        // Middleware handled in routes or via gates in index
    }

    /**
     * Menampilkan daftar user.
     */
    public function index()
    {
        return view('users.index', [
            'users' => User::with('role')->latest()->paginate(10),
            'title' => 'Daftar Pengguna'
        ]);
    }

    /**
     * Menampilkan form tambah user.
     */
    public function create()
    {
        return view('users.create', [
            'title' => 'Tambah User Baru',
            'roles' => Role::all()
        ]);
    }

    /**
     * Menyimpan data user baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:5',
            'role' => 'required|exists:roles,slug', // Cek apakah slug role ada di table roles
            'phone' => 'nullable|max:15',
            'department' => 'required|max:100',
            'position' => 'required|max:100',
        ]);

        // 2. Ambil Role ID berdasarkan Slug
        $role = Role::where('slug', $request->role)->firstOrFail();
        $validatedData['role_id'] = $role->id;
        
        // Hapus key 'role' agar tidak error saat create (karena kolom 'role' sudah dihapus)
        unset($validatedData['role']);

        // 3. Auto Generate Employee ID (NIP Otomatis)
        $validatedData['employee_id'] = $this->generateEmployeeId();

        // 4. Hash Password (Amankan password) & Buat User
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        $user = User::create($validatedData);


        // 5. Sync Permissions (Custom User Permissions)
        if ($request->has('permissions')) {
            // Ambil semua permission yang ada di DB berdasarkan slug yang dikirim
            $permissions = \App\Models\Permission::whereIn('slug', $request->permissions)->pluck('id');
            $user->permissions()->sync($permissions);
        }

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
            'suggestedId' => $this->generateEmployeeId(),
            'roles' => Role::all() // Kirim data roles jika mau dropdown dinamis (opsional)
        ]);
    }

    /**
     * Memperbarui data user yang sudah ada.
     */
    public function update(Request $request, User $user)
    {
        // =====================================================
        // VALIDASI KEAMANAN: CEGAH SUPER ADMIN BUNUH DIRI
        // =====================================================
        
        // 1. Cek apakah user yang sedang diedit adalah Super Admin
        $isEditingSuperAdmin = $user->role?->slug === 'super_admin';
        
        // 2. Cek apakah role baru yang dipilih BUKAN super_admin (downgrade)
        $isDowngrading = $request->role !== 'super_admin';
        
        // 3. Jika user mengedit dirinya sendiri DAN mencoba downgrade dari super_admin
        if ($isEditingSuperAdmin && $isDowngrading && auth()->id() === $user->id) {
            return back()->with('loginError', 'Tidak bisa menurunkan role Super Admin untuk akun Anda sendiri! Ini akan mengunci Anda dari sistem.')
                         ->withInput();
        }
        
        // 4. PERLINDUNGAN TAMBAHAN: Pastikan minimal ada 1 Super Admin tersisa
        if ($isEditingSuperAdmin && $isDowngrading) {
            $superAdminCount = User::whereHas('role', fn($q) => $q->where('slug', 'super_admin'))->count();
            
            if ($superAdminCount <= 1) {
                return back()->with('loginError', 'Tidak bisa mengubah role! Sistem membutuhkan minimal 1 Super Admin aktif.')
                             ->withInput();
            }
        }
        
        // =====================================================
        // VALIDASI INPUT NORMAL
        // =====================================================
        $rules = [
            'name' => 'required|max:255',
            'role' => 'required|exists:roles,slug',
            'phone' => 'nullable|max:15',
            'department' => 'required|max:100',
            'position' => 'required|max:100',
        ];

        // Validasi Email & NIP (hanya jika berubah) agar tidak error 'sudah terpakai'
        if($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }
        if($request->employee_id != $user->employee_id) {
            $rules['employee_id'] = 'required|unique:users|max:20';
        }

        $validatedData = $request->validate($rules);

        // Jika password diisi, hash password baru.
        if($request->password) {
            $validatedData['password'] = Hash::make($request->password);
        }

        // Ambil ID Role Baru
        $role = Role::where('slug', $request->role)->firstOrFail();
        $validatedData['role_id'] = $role->id;
        unset($validatedData['role']);

        $user->update($validatedData);
        
        // Sync Custom Permissions
        if ($request->has('permissions')) {
            $permissions = \App\Models\Permission::whereIn('slug', $request->permissions)->pluck('id');
            $user->permissions()->sync($permissions);
        } else {
             // Jika tidak ada permission yang dikirim (unchecked all), kosongkan custom permission
             $user->permissions()->detach();
        }
        
        // Hapus Cache Permission agar user langsung dapat efeknya (Global cache clear for simplicity)
        // Idealnya hanya clear jika permissions berubah, tapi karena permission nempel di role, aman.
        \Illuminate\Support\Facades\Cache::forget('app_permissions');
        
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
        // 1. Mencegah user menghapus dirinya sendiri
        if(auth()->id() == $user->id) {
            return back()->with('loginError', 'Tidak bisa menghapus akun sendiri!');
        }
        
        // 2. PERLINDUNGAN: Cegah hapus Super Admin terakhir
        if ($user->role?->slug === 'super_admin') {
            $superAdminCount = User::whereHas('role', fn($q) => $q->where('slug', 'super_admin'))->count();
            
            if ($superAdminCount <= 1) {
                return back()->with('loginError', 'Tidak bisa menghapus Super Admin terakhir! Sistem membutuhkan minimal 1 Super Admin.');
            }
        }
        
        $user->delete();
        return redirect('/users')->with('success', 'User dihapus.');
    }
}