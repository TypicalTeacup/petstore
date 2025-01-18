<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Pet extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function toArray()
    {
        $attrs = parent::toArray();
        // Removes pivot from output
        unset($attrs['pivot']);

        // Add photo urls
        if (isset($attrs['id'])) {
            $id = $attrs['id'];
            $storagePath = "img/pet/$id";
            $files = Storage::disk('public')->files($storagePath);
            $photoUrls = array_map(fn($file) => "/storage/$file", $files);
            $attrs['photoUrls'] = $photoUrls;
        }

        return $attrs;
    }
}
