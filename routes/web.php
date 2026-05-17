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
    // ЕДИНЫЙ ДАШБОРД — URL остаётся /dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Можно оставить как есть, если фронтендер сделал универсальный Dashboard.vue
        return Inertia::render('Dashboard', [
            'role' => $user->role,
        ]);
    })->name('dashboard');

    // Старые URL artist/dashboard и label/dashboard тоже пусть работают, если нужны
    Route::middleware('role:label')->prefix('label')->name('label.')->group(function () {
        Route::get('/dashboard', [LabelDashboardController::class, 'index'])->name('dashboard');
    });

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