<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistController extends Controller
{
    /**
     * Список артистов лейбла.
     */
    public function index()
    {
        // Если используешь Policy — оставь, иначе можно убрать строку
        // $this->authorize('viewAny', Artist::class);

        $user = auth()->user();
        $role = is_string($user->role) ? $user->role : ($user->role->value ?? null);

        // Если зашёл артист — редирект на свой профиль
        if ($role === 'artist') {
            $artist = Artist::where('user_id', $user->id)->first();
            if ($artist) {
                return redirect()->route('artists.show', $artist);
            }
            abort(403, 'У вас нет привязанного профиля артиста');
        }

        // Лейбл видит артистов, у которых label_id = его users.id
        $artists = Artist::where('label_id', $user->id)
            ->with('user')
            ->latest()
            ->get();

        return Inertia::render('Artists/Index', [
            'artists'        => $artists,
            'pendingArtists' => [], // пока пустой, чтобы фронт не падал
            'label'          => $user->only(['id', 'name', 'email']),
        ]);
    }

    /**
     * Детальная страница артиста.
     */
    public function show(Artist $artist)
    {
        // $this->authorize('view', $artist);

        $artist->load(['user', 'songs']);

        return Inertia::render('Artists/Show', [
            'artist' => $artist,
        ]);
    }

    /**
     * Форма создания артиста лейблом.
     */
    public function create()
    {
        // $this->authorize('create', Artist::class);
        return Inertia::render('Artists/Create');
    }

    /**
     * Создание нового артиста лейблом (сразу привязанным).
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Artist::class);

        $request->validate([
            'email'      => 'required|email|unique:users,email',
            'name'       => 'required|string|max:255',
            'stage_name' => 'required|string|max:255',
            'password'   => 'required|string|min:8',
        ]);

        $label = auth()->user();

        \DB::transaction(function () use ($request, $label) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'role'     => 'artist', // если используешь enum/spatie
            ]);

            // Если используешь Spatie — раскомментируй
            // $user->assignRole('artist');

            Artist::create([
                'user_id'    => $user->id,
                'label_id'   => $label->id,
                'stage_name' => $request->stage_name,
                'real_name'  => $request->name,
                'status'     => 'approved',
            ]);
        });

        return redirect()->route('artists.index')
            ->with('success', 'Артист добавлен');
    }

    /**
     * Редактирование артиста.
     */
    public function edit(Artist $artist)
    {
        // $this->authorize('update', $artist);
        return Inertia::render('Artists/Edit', [
            'artist' => $artist,
        ]);
    }

    /**
     * Обновление данных артиста.
     */
    public function update(Request $request, Artist $artist)
    {
        // $this->authorize('update', $artist);

        $validated = $request->validate([
            'stage_name' => 'required|string|max:255',
            'real_name'  => 'nullable|string|max:255',
            'bio'        => 'nullable|string',
        ]);

        $artist->update($validated);

        return redirect()->route('artists.show', $artist)
            ->with('success', 'Данные обновлены');
    }

    /**
     * Удаление артиста.
     */
    public function destroy(Artist $artist)
    {
        // $this->authorize('delete', $artist);

        $artist->delete();

        return redirect()->route('artists.index')
            ->with('success', 'Артист удалён');
    }

    /**
     * Принять (привязать) артиста к лейблу.
     * POST /artists/{artist}/approve
     */
    public function approve(Artist $artist)
    {
        // $this->authorize('approve', $artist);

        $artist->update([
            'label_id' => auth()->id(),
            'status'   => 'approved',
        ]);

        return back()->with('success', 'Артист принят в лейбл');
    }
}