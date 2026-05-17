<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ArtistDashboardController extends Controller
{
    public function index(){
        return Inertia::render('Artist/Dashboard');
    }
}