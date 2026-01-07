<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi balik ke aset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Relasi ke user aktor (yang melakukan perubahan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}