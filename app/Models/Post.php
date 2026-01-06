<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Pastikan ini ada
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory; // Aktifkan Factory biar bisa bikin data palsu

    protected $guarded = ['id']; // Biar semua kolom boleh diisi kecuali ID

    // Relasi: Post "Milik" User (Author)
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}