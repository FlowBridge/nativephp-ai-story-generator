<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class StoryElementType extends Model
{
    protected $guarded = [];

    public function elements(): HasMany
    {
        return $this->hasMany(StoryElement::class);
    }

    public function getImageAttribute(): string
    {

        return config('filesystems.default') === 's3' ? Storage::url($this->image) : $this->image;
    }
}
