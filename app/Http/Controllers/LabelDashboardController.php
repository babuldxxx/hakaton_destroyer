<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LabelDashboardController extends Controller
{
    public function index(){
        return Inertia::render('Label/Dashboard');
    }
}
