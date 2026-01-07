<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    // Semua field boleh diisi massal kecuali id
    protected $guarded = ['id'];

    // Relasi ke user pemegang aset (Holder)
    public function holder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke history perubahan aset
    public function histories()
    {
        return $this->hasMany(AssetHistory::class)->latest();
    }
}