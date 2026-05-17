<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongAuthor;
use Illuminate\Http\Request;

class SongAuthorController extends Controller
{
    public function store(Request $request, Song $song)
    {
        $validated = $request->validate([
            'artist_id'        => 'required|exists:artists,id',
            'share_percentage' => 'required|numeric|min:0|max:100',
            'role'             => 'required|string',
            'rights_type'      => 'nullable|string',
        ]);

        if ($song->songAuthors()->where('artist_id', $validated['artist_id'])->exists()) {
            return back()->withErrors(['artist_id' => 'Этот артист уже добавлен']);
        }

        $song->songAuthors()->create($validated);

        return back()->with('success', 'Автор добавлен');
    }

    public function update(Request $request, Song $song, SongAuthor $author)
    {
        if ($author->song_id !== $song->id) abort(404);

        $validated = $request->validate([
            'share_percentage' => 'sometimes|numeric|min:0|max:100',
            'role'             => 'sometimes|string',
            'rights_type'      => 'nullable|string',
        ]);

        $author->update($validated);

        return back()->with('success', 'Доля обновлена');
    }

    public function destroy(Song $song, SongAuthor $author)
    {
        if ($author->song_id !== $song->id) abort(404);
        $author->delete();

        return back()->with('success', 'Автор удалён');
    }
}