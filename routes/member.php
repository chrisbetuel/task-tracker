<?php

use App\Http\Controllers\TeamMember\DashboardController;
use App\Http\Controllers\TeamMember\ProjectController;
use App\Http\Controllers\TeamMember\TaskController;
use App\Http\Controllers\TeamMember\TeammateController;
use App\Http\Controllers\TeamMember\TimeLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'team_member'])->prefix('member')->name('member.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('tasks', [TaskController::class, 'myTasks'])->name('tasks.my-tasks');
    Route::get('tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/unassigned', [TaskController::class, 'unassigned'])->name('tasks.unassigned');
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('tasks/{task}/status', [TaskController::class, 'setStatus'])->name('tasks.status');
    Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('tasks/{task}/claim', [TaskController::class, 'claim'])->name('tasks.claim');
    Route::post('tasks/{task}/accept', [TaskController::class, 'accept'])->name('tasks.accept');
    Route::post('tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::post('tasks/{task}/start', [TaskController::class, 'start'])->name('tasks.start');
    Route::post('tasks/{task}/blockage', [TaskController::class, 'reportBlockage'])->name('tasks.blockage');
    Route::post('tasks/{task}/resolve-blockage', [TaskController::class, 'resolveBlockage'])->name('tasks.resolve-blockage');
    Route::post('tasks/{task}/done', [TaskController::class, 'markDone'])->name('tasks.done');
    Route::post('tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');
    Route::post('tasks/{task}/unassign', [TaskController::class, 'unassign'])->name('tasks.unassign');
    Route::get('tasks/{task}/time-logs/create', [TimeLogController::class, 'create'])->name('time-logs.create');
    Route::post('time-logs', [TimeLogController::class, 'store'])->name('time-logs.store');
    Route::get('teammates', [TeammateController::class, 'index'])->name('teammates.index');
    Route::get('teammates/create', [TeammateController::class, 'create'])->name('teammates.create');
    Route::post('teammates', [TeammateController::class, 'store'])->name('teammates.store');
    Route::get('teammates/{teammate}', [TeammateController::class, 'show'])->name('teammates.show');
    Route::post('tasks/{task}/assign-member', [TaskController::class, 'assignToMember'])->name('tasks.assign-member');
});
