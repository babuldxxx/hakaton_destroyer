<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\ArtistInvitationController;
use App\Http\Controllers\LabelDashboardController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SongAuthorController;
use App\Http\Controllers\PlatformController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// === ГЛАВНАЯ ===
Route::get('/', fn () => redirect()->route('login'));

// === ГОСТЕВЫЕ РОУТЫ ИНВАЙТОВ (Доступны без авторизации из писем) ===
Route::get('/invitations/{token}', [ArtistInvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}/accept',  [ArtistInvitationController::class, 'accept'])->name('artists.invitations.accept');
Route::post('/invitations/{token}/decline', [ArtistInvitationController::class, 'decline'])->name('artists.invitations.decline');

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
    
    // --- ВЫПЛАТЫ (Payouts) ---
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');
    Route::patch('/payouts/{payout}/pay', [PayoutController::class, 'pay'])->name('payouts.pay');

    // --- ТРЕКИ ---
    Route::resource('tracks', SongController::class)
        ->parameters(['tracks' => 'song'])
        ->names('tracks');

    // Роут для добавления earning
    Route::post('/tracks/{song}/earnings', [SongController::class, 'storeEarning'])
        ->name('tracks.earnings.store');

    // Авторы трека
    Route::post('tracks/{song}/authors', [SongAuthorController::class, 'store'])->name('song-authors.store');
    Route::put('tracks/{song}/authors/{author}', [SongAuthorController::class, 'update'])->name('song-authors.update');
    Route::delete('tracks/{song}/authors/{author}', [SongAuthorController::class, 'destroy'])->name('song-authors.destroy');

    // --- АРТИСТЫ, РОСТЕР И ИНВАЙТЫ ---
    Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
    Route::post('/artists/{artist}/approve', [ArtistController::class, 'approve'])->name('artists.approve');
    Route::resource('artists', ArtistController::class)->except(['index']);

    // Инвайты по email (отдельно)
    Route::post('/artists/invitations', [ArtistInvitationController::class, 'store'])->name('artists.invitations.store');
    Route::delete('/artists/invitations/{invitation}', [ArtistInvitationController::class, 'destroy'])->name('artists.invitations.destroy');

    // --- ПЛАТФОРМЫ ---
    Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');

    // --- ПРОФИЛЬ ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';