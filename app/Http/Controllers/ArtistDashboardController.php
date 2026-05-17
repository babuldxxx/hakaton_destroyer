<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistDashboardController extends Controller
{
    public function index(){
        $stats = [
            'balance' => '87 500 ₽',
            'total_income' => '524 300 ₽',
            'tracks_count' => '1',
            'tracks_sub' => '+2 за месяц',
            'paid_out' => '436 800 ₽',
        ];

        return Inertia::render('Dashboard/Artist', ['stats' => $stats]);
    }
}
