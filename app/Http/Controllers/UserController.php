<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user.view')->only(['index', 'show']);
        $this->middleware('can:user.create')->only(['create', 'store']);
        $this->middleware('can:user.edit')->only(['edit', 'update']);
        $this->middleware('can:user.delete')->only(['destroy']);
    }

    public function index()
    {
        $query = User::with(['assets' => function($q) {
            $q->where('status', 'deployed');
        }])->latest();

        // Fitur Pencarian (Search)
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

        // Fitur Filter Role
        if (request('role')) {
            $query->where('role', request('role'));
        }

        return view('users.index', [
            'title' => 'Manajemen Pengguna',
            'users' => $query->get()
        ]);
    }

    public function create()
    {
        return view('users.create', ['title' => 'Tambah User']);
    }

    public function store(Request $request)
    {
        // 1. Validasi Dasar (Tanpa employee_id karena auto)
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:5',
            'role' => 'required|in:admin,user,super_admin,service_center',
            'phone' => 'nullable|max:15',
            'department' => 'required|max:100',
            'position' => 'required|max:100',
            'permissions' => 'nullable|array' // Validasi permissions sebagai array
        ]);

        // 2. Auto Generate Employee ID (NIP)
        $validatedData['employee_id'] = $this->generateEmployeeId();

        // 3. Hash Password & Create User
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        // Simpan permissions (otomatis di-cast ke JSON oleh Model)
        $user = User::create($validatedData);

        return redirect('/users')->with('success', 'User berhasil ditambahkan! NIP Otomatis: ' . $user->employee_id);
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'title' => 'Edit User',
            'user' => $user,
            'suggestedId' => $this->generateEmployeeId() // Kirim ID saran ke view untuk tombol auto-generate
        ]);
    }

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

        // Cek keunikan email & NIP jika berubah
        if($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }
        if($request->employee_id != $user->employee_id) {
            $rules['employee_id'] = 'required|unique:users|max:20';
        }

        $validatedData = $request->validate($rules);

        if($request->password) {
            $validatedData['password'] = Hash::make($request->password);
        }

        // Jika permissions tidak dikirim (unchecked all), set kosong
        if (!isset($validatedData['permissions'])) {
            $validatedData['permissions'] = [];
        }

        $user->update($validatedData);
        return redirect('/users')->with('success', 'Data user diperbarui!');
    }

    private function generateEmployeeId()
    {
        // Format: EMP-YYYYMM-XXX (Contoh: EMP-202401-001)
        $prefix = 'EMP-' . date('Ym') . '-';
        $lastUser = User::where('employee_id', 'like', $prefix . '%')
                        ->orderBy('employee_id', 'desc')
                        ->first();
        
        if ($lastUser) {
            $lastNumber = intval(substr($lastUser->employee_id, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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