<?php

use App\Http\Controllers\ArtistController;
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

    // Тестовая загрузка
    Route::get('/test-upload', [FileTestController::class, 'uploadForm']);
    Route::post('/test-upload', [FileTestController::class, 'upload']);

    Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');

    // === ТРЕКИ ===
    // Сохранение дохода — ОБЯЗАТЕЛЬНО до или после resource, внутри auth
    Route::post('/tracks/{song}/earnings', [SongController::class, 'storeEarning'])
        ->name('tracks.earnings.store');

    Route::resource('tracks', SongController::class)
        ->parameters(['tracks' => 'song'])
        ->names('tracks');

    // Авторы трека
    Route::post('tracks/{song}/authors', [SongAuthorController::class, 'store'])->name('song-authors.store');
    Route::put('tracks/{song}/authors/{author}', [SongAuthorController::class, 'update'])->name('song-authors.update');
    Route::delete('tracks/{song}/authors/{author}', [SongAuthorController::class, 'destroy'])->name('song-authors.destroy');

    // Артисты
    Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
    Route::get('/artists/create', [ArtistController::class, 'create'])->name('artists.create');
    Route::post('/artists', [ArtistController::class, 'store'])->name('artists.store');
    Route::get('/artists/{artist}', [ArtistController::class, 'show'])->name('artists.show');
});

require __DIR__.'/auth.php';