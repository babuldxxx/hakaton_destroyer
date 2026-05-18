<?php

use App\Http\Controllers\PlatformController;
use Illuminate\Support\Facades\Route;

Route::apiResource('platforms', PlatformController::class);