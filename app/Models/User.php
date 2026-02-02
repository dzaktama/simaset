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
    ];

    // Daftar kolom yang boleh diisi secara massal (Mass Assignment)
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Ganti 'role' & 'permissions' dengan 'role_id'
        'employee_id',
        'phone',      
        'department', 
        'position',   
    ];

    /**
     * Relasi ke Role (Many-to-One)
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi ke Permission (Many-to-Many)
     * Untuk Custom Permission per User (Override Role Defaults)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    /**
     * Fungsi Utama Cek Hak Akses (Has Permission)
     * Dipanggil oleh Gate di AppServiceProvider.
     * 
     * @param string $permission Nama izin (contoh: 'asset.create')
     * @return bool
     */
    public function hasPermission($permission)
    {
        // 1. Super Admin Bypass (Cek role slug)
        // Gunakan optional() untuk menghindari error jika role_id null
        if (optional($this->role)->slug === 'super_admin') {
            return true;
        }

        // 2. Cek Custom Permission User (Override)
        if ($this->permissions->contains('slug', $permission)) {
            return true;
        }

        // 3. Cek Permission dari Role Default
        return $this->role ? $this->role->permissions->contains('slug', $permission) : false;
    }
}