<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/tasks/create', [App\Http\Controllers\TeamMember\TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [App\Http\Controllers\TeamMember\TaskController::class, 'store'])->name('tasks.store');
    Route::post('/tasks/{task}/assets/image', [App\Http\Controllers\AssetController::class, 'storeImage'])->name('assets.store-image');
    Route::post('/tasks/{task}/assets/video', [App\Http\Controllers\AssetController::class, 'storeVideo'])->name('assets.store-video');
    Route::delete('/assets/{asset}', [App\Http\Controllers\AssetController::class, 'destroy'])->name('assets.destroy');
});


require __DIR__ . '/admin.php';
require __DIR__ . '/manager.php';
require __DIR__ . '/member.php';
