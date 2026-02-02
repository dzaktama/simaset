<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'slug'];

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'slug'); // Mapping 'role' column in users table to 'slug' here
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
