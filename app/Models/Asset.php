<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    // 1. IZINKAN SEMUA KOLOM PENTING (Termasuk Tanggal Baru)
    protected $fillable = [
        'name',
        'serial_number',
        'quantity',      
        'status',
        'description',
        'condition_notes',
        'image',
        'image2',
        'image3',
        'purchase_date',
        'user_id',
        'assigned_date',
        'return_date',
    ];

    // 2. FORMAT TANGGAL AGAR BISA OLAH JAM/MENIT
    protected $casts = [
        'purchase_date' => 'date',
        'assigned_date' => 'datetime',
        'return_date' => 'datetime',
    ];
// Relasi untuk mengambil request yang sedang aktif (status 'approved' / deployed)
    public function activeRequest()
    {
        return $this->hasOne(AssetRequest::class)->where('status', 'approved')->latest();
    }
    // 3. SCOPE FILTER 
    // Method ini menangani logika pencarian dan filter status di halaman index
    public function scopeFilter($query, array $filters)
    {
        // Filter Pencarian (Search)
        $query->when($filters['search'] ?? false, function($query, $search) {
            return $query->where(function($query) use ($search) {
                 $query->where('name', 'like', '%' . $search . '%')
                       ->orWhere('serial_number', 'like', '%' . $search . '%')
                       ->orWhere('description', 'like', '%' . $search . '%');
             });
        });

        // Filter Status (Dropdown)
        $query->when($filters['status'] ?? false, function($query, $status) {
             if($status == 'all') return $query;
             return $query->where('status', $status);
        });
    }

    // 4. RELASI KE PEMEGANG ASET (USER)
    public function holder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 5. RELASI KE REQUEST TERAKHIR (Untuk Cek History)
    public function latestApprovedRequest()
    {
        return $this->hasOne(AssetRequest::class)->where('status', 'approved')->latest();
    }

    /**
     * Generate QR Code untuk scanning
     */
    public function getQrCodeAttribute(): string
    {
        return app(\App\Services\AssetService::class)->generateQrCodeDataUrl($this);
    }
}