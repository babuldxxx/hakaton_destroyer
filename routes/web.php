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

// ВРЕМЕННО без middleware auth, чтобы верстать без логина
//Route::get('/dashboard', function () {
//    if (auth()->check()) {
//        $user = auth()->user();
//        if ($user->role === 'label') {
//            return redirect()->route('label.dashboard');
//        }
//        return redirect()->route('artist.dashboard');
//    }
//
//    return Inertia::render('Dashboard/Artist', [
//        'stats' => [
//            'balance' => '87 500 ₽',
//            'total_income' => '524 300 ₽',
//            'tracks_count' => '1',
//            'tracks_sub' => '+2 за месяц',
//            'paid_out' => '436 800 ₽',
//        ]
//    ]);
//})->name('dashboard');

Route::get('/tracks', function () {
    return Inertia::render('Tracks/Index');
})->name('tracks');

Route::get('/finances', function () {
    return Inertia::render('Finances/Index');
})->name('finances');

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
