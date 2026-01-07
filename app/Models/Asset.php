<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    // Guarded empty array means all columns are fillable (mass assignment protection)
    protected $guarded = ['id'];

    // Eager Loading default (biar query ringan saat dipanggil)
    protected $with = ['holder'];

    // Casting tipe data otomatis
    protected $casts = [
        'purchase_date' => 'date',
    ];

    /**
     * Relasi: Aset ini dipegang oleh siapa?
     * Menggunakan istilah 'holder' agar lebih kontekstual dibanding 'user'.
     */
    public function holder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Helper untuk cek status warna badge (untuk UI nanti)
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'available' => 'green',
            'deployed' => 'blue',
            'maintenance' => 'yellow',
            'broken' => 'red',
            default => 'gray',
        };
    }
    // Relasi: Aset bisa punya banyak history request
    public function requests()
    {
        return $this->hasMany(AssetRequest::class);
    }
}