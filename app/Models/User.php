<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // HAPUS BARIS PASSWORD => HASHED DI SINI
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'phone',      
        'department', 
        'position',   
    ];
}