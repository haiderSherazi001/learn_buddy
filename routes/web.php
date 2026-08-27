<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\RoomController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/lobby', [LobbyController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('lobby');

Route::post('/queue/join', [QueueController::class, 'join'])->name('queue.join');

Route::post('/queue/leave', [QueueController::class, 'leave'])->name('queue.leave');

Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

Route::post('/rooms/{room}/leave', [RoomController::class, 'leave'])->name('rooms.leave');

require __DIR__.'/auth.php';
