<?php

use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomParticipantController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/new-dm', [ChatController::class, 'newDirectMessage'])->name('chat.direct.new');
    Route::post('/chat/direct/{user}', [ChatController::class, 'startDirectMessage'])->name('chat.direct.start');
    Route::get('/chat/{room}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{room}/messages', [ChatController::class, 'store'])->name('messages.store');
    Route::delete('/chat/{room}/leave', [ChatController::class, 'leave'])->name('chat.leave');

    //Admin only
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // User management
        Route::resource('users', UserController::class);

        // Rooms CRUD
        Route::resource('rooms', RoomController::class)->except(['show', 'index']);

        // Users Management
        Route::get('rooms/{room}/participants', [RoomParticipantController::class, 'index'])->name('rooms.participants.index');
        Route::post('rooms/{room}/participants', [RoomParticipantController::class, 'store'])->name('rooms.participants.store');
        Route::delete('rooms/{room}/participants/{user}', [RoomParticipantController::class, 'destroy'])->name('rooms.participants.destroy');
    });
});

// User profile management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
