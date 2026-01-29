<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Relasi One-to-Many: Satu User bisa memegang banyak Aset.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    // Kolom yang dijaga (tidak bisa diisi massal)
    protected $guarded = ['id'];

    // Kolom yang disembunyikan saat model diubah jadi Array/JSON (keamanan)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi Tipe Data Otomatis (Casting)
     * 'permissions' => 'array' akan otomatis mengubah JSON dari database menjadi Array PHP
     * saat kita mengakses $user->permissions.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
    ];

    // Daftar kolom yang boleh diisi secara massal (Mass Assignment)
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'phone',      
        'department', 
        'position',   
        'permissions',
    ];

    /**
     * Fungsi Utama Cek Hak Akses (Has Permission)
     * Dipanggil oleh Gate di AppServiceProvider.
     * 
     * @param string $permission Nama izin (contoh: 'asset.create')
     * @return bool True jika punya izin, False jika tidak
     */
    public function hasPermission($permission)
    {
        // 1. Super Admin: Punya akses mutlak (God Mode)
        // Jika role-nya super_admin, selalu return true tanpa cek array.
        if ($this->role === 'super_admin') {
            return true;
        }

        // 2. User Biasa / Admin Biasa:
        // Cek apakah string permission ada di dalam array permissions miliknya.
        // Operator '?? []' digunakan untuk mencegah error jika permissions bernilai null.
        return in_array($permission, $this->permissions ?? []);
    }
}