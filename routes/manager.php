<?php

use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ProjectController;
use App\Http\Controllers\Manager\TaskController;
use App\Http\Controllers\Manager\TeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::post('tasks/{task}/status', [TaskController::class, 'setStatus'])->name('tasks.status');
    Route::get('team', [TeamController::class, 'index'])->name('team.index');
});
