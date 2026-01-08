<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    // Agar otomatis ambil data user & asset pas dipanggil
    protected $with = ['user', 'asset'];

    protected $fillable = [
        'user_id',
        'asset_id',
        'quantity', // <--- Tambahkan ini
        'request_date',
        'return_date',
        'reason',
        'status',
        'admin_note'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}