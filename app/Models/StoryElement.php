<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryElement extends Model
{
    protected $fillable = ['name', 'description', 'type_id', 'image'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(StoryElementType::class);
    }
}
