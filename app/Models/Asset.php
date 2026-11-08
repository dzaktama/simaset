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

    // [FIX] Relasi riwayat aset. AssetController@show memanggil $asset->histories(),
    // tetapi relasi ini belum ada sehingga halaman detail aset error 500.
    public function histories()
    {
        return $this->hasMany(AssetHistory::class);
    }

    // [New] Maintenance Relation
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class)->latest('start_date');
    }

    // Relasi untuk mengambil request yang sedang aktif
    // [PRESENTASI FR-02 #9] Relasi hasOne: peminjaman berstatus approved yang masih aktif. Dipakai
    //     untuk mengetahui siapa yang sedang memegang aset.
    public function activeRequest()
    {
        // [PERF] without('asset'): AssetRequest punya $with = ['user','asset'];
        // relasi asset tidak perlu dimuat ulang karena parent-nya sudah aset ini.
        return $this->hasOne(AssetRequest::class)->whereIn('status', ['approved', 'pending_return'])->latest()->without('asset');
    }

    // [PERF] Tiket perbaikan yang sedang berjalan (dipakai tombol aksi katalog, menggantikan query per baris)
    public function activeMaintenance()
    {
        return $this->hasOne(Maintenance::class)->where('status', 'on_process')->latest();
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
        // [PERF] Jika relasi sudah di-eager-load (walaupun hasilnya null), JANGAN query ulang.
        // Sebelumnya aset tanpa peminjam aktif selalu memicu query baru (N+1).
        return $this->activeRequestLoaded()?->user;
    }

    /**
     * [PERF] Muat relasi activeRequest sekali saja per instance (lazy load ter-cache),
     * lalu pakai hasilnya untuk accessor holder / assigned_date / return_date.
     */
    protected function activeRequestLoaded()
    {
        if (!$this->relationLoaded('activeRequest')) {
            $this->load('activeRequest.user');
        }
        return $this->getRelation('activeRequest');
    }

    // 5. ATRIBUT BARU: AMBIL TANGGAL PINJAM DARI RELASI
    public function getAssignedDateAttribute()
    {
        return $this->activeRequestLoaded()?->borrowed_at;
    }

    // 6. ATRIBUT BARU: AMBIL TANGGAL KEMBALI DARI RELASI
    public function getReturnDateAttribute()
    {
        return $this->activeRequestLoaded()?->return_date;
    }

    /**
     * Generate QR Code untuk scanning
     */
    public function getQrCodeAttribute(): string
    {
        return app(\App\Services\AssetService::class)->generateQrCodeDataUrl($this);
    }
}