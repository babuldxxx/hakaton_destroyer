<?php

use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\FileTestController;
use App\Http\Controllers\LabelDashboardController;
use App\Http\Controllers\ProfileController;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'label') {
            return redirect()->route('label.dashboard');
        }
        return redirect()->route('artist.dashboard');
    })->name('dashboard');

    // Группа лейбла
    Route::middleware('role:label')->prefix('label')->name('label.')->group(function () {
        Route::get('/dashboard', [LabelDashboardController::class, 'index'])->name('dashboard');
    });

    // Группа артиста
    Route::middleware('role:artist')->prefix('artist')->name('artist.')->group(function () {
        Route::get('/dashboard', [ArtistDashboardController::class, 'index'])->name('dashboard');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/test-upload', [FileTestController::class, 'uploadForm']);
    Route::post('/test-upload', [FileTestController::class, 'upload']);
});

require __DIR__.'/auth.php';
