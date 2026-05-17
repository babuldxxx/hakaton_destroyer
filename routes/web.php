<?php

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
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Artist', [
        'stats' => [
            'balance' => '87 500 ₽',
            'total_income' => '524 300 ₽',
            'tracks_count' => '1',
            'tracks_sub' => '+2 за месяц',
            'paid_out' => '436 800 ₽',
        ]
    ]);
})->name('dashboard');

Route::get('/tracks', function () {
    return Inertia::render('Tracks/Index');
})->name('tracks');

Route::get('/finances', function () {
    return Inertia::render('Finances/Index');
})->name('finances');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';