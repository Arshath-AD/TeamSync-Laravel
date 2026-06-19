<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Projects Module
    Route::resource('projects', \App\Http\Controllers\ProjectController::class)->except(['show']);
    Route::get('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{project}/priority', [\App\Http\Controllers\ProjectController::class, 'updatePriority'])->name('projects.updatePriority');

    // Project Members Module
    Route::get('/projects/{project}/members', [\App\Http\Controllers\ProjectMemberController::class, 'index'])->name('projects.members.index');
    Route::post('/projects/{project}/members', [\App\Http\Controllers\ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::delete('/projects/{project}/members/{user}', [\App\Http\Controllers\ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');

    // Tasks Module
    Route::get('/tasks/board', [\App\Http\Controllers\TaskController::class, 'board'])->name('tasks.board');
    Route::get('/my-tasks', [\App\Http\Controllers\TaskController::class, 'myTasks'])->name('tasks.myTasks');
    Route::patch('/tasks/{task}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::patch('/tasks/{task}/priority', [\App\Http\Controllers\TaskController::class, 'updatePriority'])->name('tasks.updatePriority');
    Route::resource('tasks', \App\Http\Controllers\TaskController::class)->except(['show']);
});

require __DIR__.'/auth.php';
