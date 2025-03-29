<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoryElementType extends Model
{
    protected $guarded = [];

    public function elements(): HasMany
    {
        return $this->hasMany(StoryElement::class);
    }
}
