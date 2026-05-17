<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class LabelDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Label/Dashboard');
    }
}