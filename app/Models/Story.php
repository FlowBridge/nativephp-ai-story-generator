<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Story extends Model
{
    protected $fillable = [
        'content',
        'audio_url',
        'story_elements'
    ];

    protected $casts = [
        'story_elements' => 'array'
    ];

    public function elements(): BelongsToMany
    {
        return $this->belongsToMany(StoryElement::class, 'story_elements');
    }
}
