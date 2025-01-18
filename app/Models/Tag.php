<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'pivot'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(Pet::class);
    }

    /**
     * Removes pivot from output
     */
    public function toArray()
    {
        $attrs = parent::toArray();
        unset($attrs['pivot']);
        return $attrs;
    }
}
