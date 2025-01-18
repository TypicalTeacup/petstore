<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}
