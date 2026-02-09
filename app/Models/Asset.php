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
        'category', // Added category to fillable
        'status',
        'description',
        'image',
        'image2', // Tambahan
        'image3', // Tambahan
        'purchase_date',
        'purchase_price', // Baru
        'useful_life_years', // Baru
        'residual_value', // Baru
        'user_id',
        'location', // Gabungan Lorong + Rak
        'lorong', // detail
        'rak', // detail
        'quantity',
        'assigned_date',
        'return_date'
    ];

    // 2. FORMAT TANGGAL
    protected $casts = [
        'purchase_date' => 'date',
        'assigned_date' => 'datetime',
        'return_date' => 'datetime',
        'purchase_price' => 'decimal:2',
        'residual_value' => 'decimal:2',
    ];

    /**
     * Hitung Nilai Buku Saat Ini (Straight Line Depreciation)
     */
    public function getCurrentValueAttribute()
    {
        // 1. Cek Data Lengkap
        if (!$this->purchase_date || !$this->purchase_price || !$this->useful_life_years) {
            return $this->purchase_price ?? 0;
        }

        // 2. Variable Dasar
        $cost = $this->purchase_price;
        $residual = $this->residual_value ?? 0;
        $lifeYears = $this->useful_life_years;
        $lifeMonths = $lifeYears * 12;

        // 3. Hitung Umur (Bulan)
        // Gunakan diffInMonths dari Carbon
        $ageMonths = $this->purchase_date->diffInMonths(now());

        // 4. Cek Jika Umur > Masa Pakai (Aset sudah habis nilai ekonomisnya)
        if ($ageMonths >= $lifeMonths) {
            return $residual;
        }

        // 5. Rumus Straight Line Per Bulan
        // Depresiasi per Bulan = (Harga Beli - Residu) / Total Bulan Pakai
        $depreciationPerMonth = ($cost - $residual) / $lifeMonths;

        // 6. Hitung Penyusutan Akumulasi
        $accumulatedDepreciation = $depreciationPerMonth * $ageMonths;

        // 7. Nilai Saat Ini
        $currentValue = $cost - $accumulatedDepreciation;

        return max($currentValue, $residual); // Jangan sampai di bawah nilai residu
    }

    /**
     * Helper: Persentase Sisa Umur (0% - 100%)
     */
    public function getDepreciationPercentageAttribute()
    {
        if (!$this->purchase_price) return 0;
        
        $initial = $this->purchase_price - ($this->residual_value ?? 0);
        if ($initial <= 0) return 0;

        $current = $this->current_value - ($this->residual_value ?? 0);
        
        return round(($current / $initial) * 100);
    }

    // --- [INI YANG DITAMBAHKAN AGAR TIDAK ERROR] ---
    // Fungsi ini wajib ada karena Controller memanggil ->with('holder')
    public function holder()
    {
        // Asumsi ada kolom user_id di tabel assets untuk pemegang saat ini
        return $this->belongsTo(User::class, 'user_id');
    }
    // -----------------------------------------------

    // [New] Maintenance Relation
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class)->latest('start_date');
    }

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