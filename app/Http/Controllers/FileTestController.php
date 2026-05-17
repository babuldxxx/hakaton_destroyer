<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class FileTestController extends Controller
{
    public function uploadForm(){
        return Inertia::render('Test/Upload');
    }

    public function upload(Request $request){
        $request->validate([
            'track' => 'required|file|mimes:wav,mp3|max:10240',
        ]);

        $path = $request->file('track')->store('tracks', 'public');
        return back()->with('success', "Файл загружен: $path");
    }
}
