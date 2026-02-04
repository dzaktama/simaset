<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $fillable = ['id', 'title', 'description', 'icon', 'color', 'roles'];

    protected $casts = [
        'roles' => 'array',
    ];

    public $incrementing = false; // Because ID is string
    protected $keyType = 'string';

    public function steps()
    {
        return $this->hasMany(GuideStep::class, 'guide_id')->orderBy('order_index');
    }
}
