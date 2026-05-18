<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlatformController extends Controller
{
    public function index()
    {
        return Inertia::render('Platforms/Index', [
            'platforms' => Platform::orderBy('name')
                ->where('is_active', true) // показывает только активные площадки
                ->get(['id', 'name', 'slug', 'icon', 'is_active'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:platforms',
            'icon'      => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        Platform::create($validated);

        return back()->with('success', 'Площадка добавлена');
    }

    public function update(Request $request, Platform $platform)
    {
        $validated = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'slug'      => 'sometimes|string|max:255|unique:platforms,slug,' . $platform->id,
            'icon'      => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $platform->update($validated);

        return back()->with('success', 'Площадка обновлена');
    }

    public function destroy(Platform $platform)
    {
        $platform->delete();

        return back()->with('success', 'Площадка удалена');
    }
}