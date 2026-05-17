<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Inertia\Inertia;

class PlatformController extends Controller
{
    public function index()
    {
        return Inertia::render('Platforms/Index', [
            'platforms' => Platform::all()
        ]);
    }
}