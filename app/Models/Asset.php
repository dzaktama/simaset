<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Tambahan biar User::class terbaca

class Asset extends Model
{
    use HasFactory;

    // 1. IZINKAN SEMUA KOLOM PENTING (Sesuai kode Mas)
    protected $fillable = [
        'name',
        'serial_number',
        'category',
        'lorong',
        'rak',
        'location',
        'quantity',
        'purchase_date',
        'status',
        'image',
        'image2',          
        'image3',          
        'description',
        'condition_notes'  
    ];

    // 2. FORMAT TANGGAL
    protected $casts = [
        'purchase_date' => 'date',
        'assigned_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    // --- [INI YANG DITAMBAHKAN AGAR TIDAK ERROR] ---
    // Fungsi ini wajib ada karena Controller memanggil ->with('holder')
    public function holder()
    {
        // Asumsi ada kolom user_id di tabel assets untuk pemegang saat ini
        return $this->belongsTo(User::class, 'user_id');
    }
    // -----------------------------------------------

    // Relasi untuk mengambil request yang sedang aktif
    public function activeRequest()
    {
        return $this->hasOne(AssetRequest::class)->where('status', 'approved')->latest();
    }

    // 3. SCOPE FILTER 
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

    // 4. ATRIBUT BARU: AMBIL DATA PEMINJAM (HOLDER) DARI RELASI
    public function getHolderAttribute()
    {
        // Cek jika ada `activeRequest` yang sudah di-load
        if ($this->relationLoaded('activeRequest') && $this->activeRequest) {
            return $this->activeRequest->user;
        }
        // Fallback jika tidak di-load
        return $this->activeRequest()->first()->user ?? null;
    }

    // 5. ATRIBUT BARU: AMBIL TANGGAL PINJAM DARI RELASI
    public function getAssignedDateAttribute()
    {
        if ($this->relationLoaded('activeRequest') && $this->activeRequest) {
            return $this->activeRequest->borrowed_at;
        }
        return $this->activeRequest()->first()->borrowed_at ?? null;
    }

    // 6. ATRIBUT BARU: AMBIL TANGGAL KEMBALI DARI RELASI
    public function getReturnDateAttribute()
    {
        if ($this->relationLoaded('activeRequest') && $this->activeRequest) {
            return $this->activeRequest->return_date;
        }
        return $this->activeRequest()->first()->return_date ?? null;
    }

    /**
     * Generate QR Code untuk scanning
     */
    public function getQrCodeAttribute(): string
    {
        return app(\App\Services\AssetService::class)->generateQrCodeDataUrl($this);
    }
}