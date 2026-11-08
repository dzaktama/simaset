<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetReturn extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Status denda
    public const FINE_NONE = 'none';
    public const FINE_UNPAID = 'unpaid';
    public const FINE_PROOF = 'proof_submitted';
    public const FINE_PAID = 'paid';
    public const FINE_WAIVED = 'waived';

    protected $casts = [
        'return_date' => 'datetime',
        'fine' => 'decimal:2',
        'fine_proof_submitted_at' => 'datetime',
        'fine_paid_at' => 'datetime',
    ];

    /** Denda yang masih harus diselesaikan (belum lunas / belum dibebaskan). */
    public function hasOutstandingFine(): bool
    {
        return in_array($this->fine_status, [self::FINE_UNPAID, self::FINE_PROOF], true);
    }

    public static function fineLabel(?string $status): array
    {
        return match ($status) {
            self::FINE_UNPAID => ['Belum Dibayar', 'bg-rose-100 text-rose-700 border-rose-200'],
            self::FINE_PROOF => ['Menunggu Konfirmasi', 'bg-amber-100 text-amber-700 border-amber-200'],
            self::FINE_PAID => ['Lunas', 'bg-emerald-100 text-emerald-700 border-emerald-200'],
            self::FINE_WAIVED => ['Dibebaskan', 'bg-slate-100 text-slate-600 border-slate-200'],
            default => ['Tanpa Denda', 'bg-slate-50 text-slate-400 border-slate-200'],
        };
    }

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

    // Admin yang mengonfirmasi pembayaran denda
    public function fineConfirmer()
    {
        return $this->belongsTo(User::class, 'fine_confirmed_by');
    }
}
