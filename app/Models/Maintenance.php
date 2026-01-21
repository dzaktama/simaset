<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'vendor_name',
        'start_date',
        'completion_date',
        'cost',
        'problem_description',
        'resolution_notes',
        'status'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
