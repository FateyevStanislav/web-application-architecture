<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\GitHubController;

Route::get('/dashboard', fn () => redirect()->route('posts.index'))
    ->middleware('auth')
    ->name('dashboard');

Route::get('/', fn () => redirect()->route('posts.index'))->name('home');

Route::resource('posts', PostController::class);

Route::post('/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

Route::get('/auth/github', [GitHubController::class, 'redirect'])->name('auth.github');
Route::get('/auth/github/callback', [GitHubController::class, 'callback']);

Route::view('/oauth/callback', 'auth.oauth-callback')->name('oauth.callback');

require __DIR__.'/auth.php';
