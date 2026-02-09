<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id', // Teknisi yang membuat tiket
        'vendor_name',
        'start_date',
        'completion_date',
        'cost',
        'problem_description',
        'resolution_notes',
        'status',
        'resolver_id'
    ];

    /**
     * Relasi ke aset yang sedang diperbaiki
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke user (teknisi/service center) yang membuat tiket
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke user (teknisi) yang menyelesaikan tiket
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolver_id');
    }
}
