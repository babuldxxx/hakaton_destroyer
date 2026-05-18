<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\LabelDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SongAuthorController;
use App\Http\Controllers\PlatformController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// === ГЛАВНАЯ ===
Route::get('/', fn () => redirect()->route('login'));

// === ВСЁ, ЧТО ТРЕБУЕТ АВТОРИЗАЦИИ ===
Route::middleware(['auth', 'verified'])->group(function () {

    // Центральный вход — редиректит по роли
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        $val = $role instanceof \BackedEnum ? $role->value : $role;

        return $val === 'artist'
            ? redirect()->route('artist.dashboard')
            : redirect()->route('label.dashboard');
    })->name('dashboard');

    // --- ДАШБОРДЫ ---
    Route::get('/label/dashboard', [LabelDashboardController::class, 'index'])->name('label.dashboard');
    Route::get('/artist/dashboard', [ArtistDashboardController::class, 'index'])->name('artist.dashboard');

    // --- ФИНАНСЫ ---
    Route::get('/finances', fn () => Inertia::render('Finances/Index'))->name('finances');

    // --- ТРЕКИ ---
    Route::resource('tracks', SongController::class)
        ->parameters(['tracks' => 'song'])
        ->names('tracks');

    // Доходы по треку (только для лейбла — проверка внутри SongController)
    Route::post('/tracks/{song}/earnings', [SongController::class, 'storeEarning'])
        ->name('tracks.earnings.store');

    // Авторы трека
    Route::post('tracks/{song}/authors', [SongAuthorController::class, 'store'])->name('song-authors.store');
    Route::put('tracks/{song}/authors/{author}', [SongAuthorController::class, 'update'])->name('song-authors.update');
    Route::delete('tracks/{song}/authors/{author}', [SongAuthorController::class, 'destroy'])->name('song-authors.destroy');

    // --- АРТИСТЫ ---
    Route::resource('artists', ArtistController::class);

    // --- ПЛАТФОРМЫ ---
    Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');

    // --- ПРОФИЛЬ ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';