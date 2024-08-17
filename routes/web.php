<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

// Home and User Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/user', [UserController::class, 'index'])->name('user');

// Dashboard Route (protected by auth middleware)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authenticated Routes Group
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Post Routes
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/show', [PostController::class, 'index'])->name('posts.show');

    // Like Routes
    Route::post('/likes/{post}/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');
    // Share Routes
    Route::post('/posts/{postId}/shares', [ShareController::class, 'store'])->name('shares.store');

    // Comment Routes
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store'])->name('comments.store');
});

// Authentication Routes (Login, Registration, etc.)
require __DIR__.'/auth.php';
