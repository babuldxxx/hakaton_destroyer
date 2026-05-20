<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\LabelDashboardController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SongAuthorController;
use App\Http\Controllers\PlatformController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('artist')) {
            return redirect()->route('artist.dashboard');
        }
        return redirect()->route('label.dashboard');
    })->name('dashboard');

    Route::middleware('role:label')->group(function () {
        Route::get('/label/dashboard', [LabelDashboardController::class, 'index'])->name('label.dashboard');
    });

    Route::middleware('role:artist')->group(function () {
        Route::get('/artist/dashboard', [ArtistDashboardController::class, 'index'])->name('artist.dashboard');
    });

    Route::get('/finances', fn () => Inertia::render('Finances/Index'))->name('finances');

    Route::resource('tracks', SongController::class)
        ->parameters(['tracks' => 'song'])
        ->names('tracks');
    Route::post('/tracks/{song}/earnings', [SongController::class, 'storeEarning'])->name('tracks.earnings.store');
    Route::post('tracks/{song}/authors', [SongAuthorController::class, 'store'])->name('song-authors.store');
    Route::put('tracks/{song}/authors/{author}', [SongAuthorController::class, 'update'])->name('song-authors.update');
    Route::delete('tracks/{song}/authors/{author}', [SongAuthorController::class, 'destroy'])->name('song-authors.destroy');

    Route::get('/artists/invitations', [ArtistController::class, 'invitations'])->name('artists.invitations');
    Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
    Route::get('/artists/{artist}', [ArtistController::class, 'show'])->name('artists.show');
    Route::post('/artists/{artist}/invite', [ArtistController::class, 'invite'])->name('artists.invite');
    Route::post('/artists/invitations/{invitation}/accept', [ArtistController::class, 'acceptInvitation'])->name('artists.invitations.accept');
    Route::post('/artists/invitations/{invitation}/decline', [ArtistController::class, 'declineInvitation'])->name('artists.invitations.decline');
    Route::get('/artists/{artist}/edit', [ArtistController::class, 'edit'])->name('artists.edit');
    Route::put('/artists/{artist}', [ArtistController::class, 'update'])->name('artists.update');
    Route::delete('/artists/{artist}', [ArtistController::class, 'destroy'])->name('artists.destroy');

    Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');
    Route::patch('/payouts/{payout}/pay', [PayoutController::class, 'pay'])->name('payouts.pay');

    Route::get('/reports', fn () => Inertia::render('Reports/Index'))->name('reports');
});

require __DIR__.'/auth.php';
