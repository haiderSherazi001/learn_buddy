<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CommitmentController;
use App\Http\Controllers\StandupController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ResourceController;


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
    Route::post('/rooms/custom', [App\Http\Controllers\RoomController::class, 'store'])->name('rooms.store');
    Route::get('/join/{invite_code}', [App\Http\Controllers\RoomController::class, 'joinViaInvite'])->name('rooms.join');
});
Route::get('/lobby', [LobbyController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('lobby');
Route::post('/queue/join', [QueueController::class, 'join'])->name('queue.join');
Route::post('/queue/leave', [QueueController::class, 'leave'])->name('queue.leave');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::post('/rooms/{room}/leave', [RoomController::class, 'leave'])->name('rooms.leave');
Route::post('/rooms/{room}/commitments', [CommitmentController::class, 'store'])->name('commitments.store');
Route::patch('/commitments/{commitment}/toggle', [CommitmentController::class, 'toggle'])->name('commitments.toggle');
Route::post('/rooms/{room}/standups', [StandupController::class, 'store'])->name('standups.store');
Route::post('/standups/{standup}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/rooms/{room}/resources', [ResourceController::class, 'store'])->name('resources.store');

require __DIR__.'/auth.php';
