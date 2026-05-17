<?php

use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\FileTestController;
use App\Http\Controllers\LabelDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SongAuthorController;
use App\Http\Controllers\PlatformController;
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
// (Закомментировано, так как ниже есть основной /dashboard с логикой ролей)
/*
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
})->name('temp.dashboard');
*/

// УДАЛЕНО: Route::get('/tracks') перебивал полноценный SongController::class

Route::get('/finances', function () {
    return Inertia::render('Finances/Index');
})->name('temp.finances');

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
    // Профиль пользователя
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Тестовая загрузка
    Route::get('/test-upload', [FileTestController::class, 'uploadForm']);
    Route::post('/test-upload', [FileTestController::class, 'upload']);

    // Platform
    Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');

    // Tracks (API + Inertia) — Переопределяем параметр на {song}
    Route::resource('tracks', SongController::class)
        ->parameters(['tracks' => 'song'])
        ->names('tracks');

    // Track authors (shares)
    Route::post('tracks/{song}/authors', [SongAuthorController::class, 'store'])->name('song-authors.store');
    Route::put('tracks/{song}/authors/{author}', [SongAuthorController::class, 'update'])->name('song-authors.update');
    Route::delete('tracks/{song}/authors/{author}', [SongAuthorController::class, 'destroy'])->name('song-authors.destroy');
});

require __DIR__.'/auth.php';