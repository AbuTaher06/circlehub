<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

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
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// p
Route::get('/profile/edit/privacy', [ProfileController::class, 'editPrivacy'])->name('profile.edit.privacy');
Route::post('/profile/update/privacy', [ProfileController::class, 'updatePrivacy'])->name('profile.update.privacy');
Route::patch('/posts/{post}/privacy', [PostController::class, 'updatePrivacy'])->name('posts.updatePrivacy');
Route::post('/profile/privacy', [ProfileController::class, 'updatePrivacy'])->name('profile.updatePrivacy');


    // Add this route to handle friend requests
   // Add this route to handle friend requests


Route::post('/friends/{user}/add', [ProfileController::class, 'addFriend'])->name('friend.add');
Route::post('/friends/{id}/confirm', [ProfileController::class, 'confirmRequest'])->name('friend.confirm');
Route::delete('/friends/{id}/delete', [ProfileController::class, 'deleteRequest'])->name('friend.delete');

// routes/web.php
Route::get('/notifications', [ProfileController::class, 'showNotifications'])->name('notifications');




    // Post Routes
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    // In routes/web.php

    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Like Routes
    Route::post('/likes/{post}/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');

    // Share Routes
    Route::post('/posts/{postId}/shares', [ShareController::class, 'store'])->name('shares.store');

    // Comment Routes
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/posts/{post}/comment', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/posts/{post}/report', [PostController::class, 'report'])->name('posts.report');
});
// routes/web.php

Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activity.edit');
Route::get('/profile/edit-cover', [ProfileController::class, 'editCover'])->name('profile.edit.cover');
Route::patch('/profile/update-cover', [ProfileController::class, 'updateCover'])->name('profile.update.cover');


// Authentication Routes (Login, Registration, etc.)
require __DIR__.'/auth.php';
