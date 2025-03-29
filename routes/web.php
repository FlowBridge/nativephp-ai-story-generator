<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/story-elements', 301)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('story-elements', function () {
    return Inertia::render('StoryElementSelection');
})->name('story-elements');

Route::get('stories', function () {
    return Inertia::render('Stories');
})->name('story-elements.stories');


// require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';
