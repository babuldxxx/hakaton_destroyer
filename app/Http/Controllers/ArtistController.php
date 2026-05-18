<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Label;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistController extends Controller
{
    /**
     * Детальная страница артиста.
     * GET /artists/{artist}
     */
    public function show(Artist $artist)
    {
        $this->authorize('view', $artist);

        $artist->load(['user', 'songs']);

        return Inertia::render('Artists/Show', [
            'artist' => $artist,
        ]);
    }

    /**
     * Форма создания артиста.
     * GET /artists/create
     */
    public function create()
    {
        $this->authorize('create', Artist::class);

        return Inertia::render('Artists/Create');
    }

    /**
     * Создание нового артиста и привязка к лейблу.
     * POST /artists
     */
    public function store(Request $request)
    {
        $this->authorize('create', Artist::class);

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'stage_name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        // Получаем лейбл текущего пользователя
        $label = Label::query()->find(auth()->user()->label_id);

        if (!$label) {
            abort(403, 'У вас нет привязанного лейбла');
        }

        // Создаём пользователя с ролью artist и привязкой к лейблу
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'artist',
            'label_id' => $label->id,
        ]);

        // Создаём артиста
        $artist = Artist::query()->create([
            'user_id' => $user->id,
            'label_id' => $label->id,
            'stage_name' => $request->stage_name,
            'real_name' => $request->name,
        ]);

        return redirect()->route('artists.show', $artist)
            ->with('success', 'Артист добавлен');
    }
}
