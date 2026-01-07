<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke User pemegang saat ini
    public function holder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi untuk mengambil Request Terakhir yang Disetujui (Untuk cek tanggal kembali)
    public function latestApprovedRequest()
    {
        return $this->hasOne(AssetRequest::class)->where('status', 'approved')->latest();
    }
}