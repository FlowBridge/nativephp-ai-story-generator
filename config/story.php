<?php

use Prism\Prism\Enums\Provider;

return [
    'max_story_length' => 100, // Max characters in the story
    'default_llm_provider' => Provider::Groq,
    'default_llm_model' => 'deepseek-r1-distill-qwen-32b',

    'eleven_labs_voices' => [
        'dutch_voice_id' => 'ARIOBKJtltx2F7r1TMzI', // Dirk by default (Requires Pro subscription on eleven labs)
        'english_voice_id' => '21m00Tcm4TlvDq8ikWAM', // Rachel by default
    ],
    'eleven_labs_model' => 'eleven_flash_v2_5',
    'eleven_labs_api_key' => env('ELEVEN_LABS_API_KEY'),
];
