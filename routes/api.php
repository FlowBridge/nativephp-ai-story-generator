<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoryElementController;
use App\Http\Controllers\StoryElementTypeController;
use App\Http\Controllers\StoryController;

Route::apiResource('story-elements', StoryElementController::class);
Route::apiResource('story-element-types', StoryElementTypeController::class)->only(['index', 'store']);

Route::post('generate-story', [StoryElementController::class, 'generateStory']);

Route::get('/stories', [StoryController::class, 'index']);
