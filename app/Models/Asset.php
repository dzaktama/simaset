<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function holder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class)->latest();
    }

    // --- TAMBAHAN BARU ---
    // Relasi untuk mengambil peminjaman yang sedang aktif/berjalan
    // Gunanya untuk mengambil 'return_date'
    public function activeLoan()
    {
        return $this->hasOne(AssetRequest::class)
                    ->where('status', 'approved') // Hanya yang sudah disetujui
                    ->latest(); // Ambil yang paling baru
    }
}