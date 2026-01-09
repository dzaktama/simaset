<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetReturn extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Request Asal
    public function assetRequest()
    {
        return $this->belongsTo(AssetRequest::class);
    }

    // Relasi ke Aset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Relasi ke User Peminjam
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Admin Verifikator
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}