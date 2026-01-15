<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    // Eager load relasi secara default agar data user/asset selalu terbawa
    protected $with = ['user', 'asset'];

    protected $fillable = [
        'user_id',
        'asset_id',
        'quantity',
        'request_date',
        'return_date',
        'borrowed_at',
        'returned_at',
        'approved_at',
        'reason',
        'status',
        'borrowing_status',
        'admin_note',
        'condition',
        'return_notes'
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