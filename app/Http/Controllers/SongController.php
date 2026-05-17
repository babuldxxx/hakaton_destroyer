<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Genre;
use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SongController extends Controller
{
    public function index()
    {
        return Inertia::render('Tracks/Index', [
            'songs' => Song::with(['artists', 'genre', 'label', 'songAuthors'])->latest()->paginate(20)
        ]);
    }

    public function create()
    {
        return Inertia::render('Tracks/Create', [
            'genres'  => Genre::select('id', 'name')->get(),
            'artists' => Artist::select('id', 'real_name', 'stage_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'lyrics'           => 'nullable|string',
            'written_at'       => 'nullable|date',
            'released_at'      => 'nullable|date',
            'label_id'         => 'nullable|exists:labels,id',
            'genre_id'         => 'nullable|exists:genres,id',
            'isrc'             => 'nullable|string|max:20',
            'mp3'              => 'nullable|file|mimes:mp3|max:51200',
            'wav'              => 'nullable|file|mimes:wav|max:102400',
            'authors'          => 'nullable|array',
            'authors.*.artist_id'       => 'required_with:authors|exists:artists,id',
            'authors.*.share_percentage'=> 'required_with:authors|numeric|min:0|max:100',
            'authors.*.role'            => 'required_with:authors|string',
            'authors.*.rights_type'     => 'nullable|string',
        ]);

        // Если лейбл не выбран — берём из профиля текущего пользователя
        if (empty($validated['label_id'])) {
            $validated['label_id'] = auth()->user()->role === 'label'
                ? auth()->id()
                : auth()->user()->label_id;
        }

        $song = DB::transaction(function () use ($validated, $request) {
            $song = new Song();
            $song->fill($validated);
            $this->storeSongFiles($song, $request);
            $song->save();

            if (!empty($validated['authors'])) {
                foreach ($validated['authors'] as $author) {
                    $song->songAuthors()->create([
                        'artist_id'        => $author['artist_id'],
                        'share_percentage' => $author['share_percentage'],
                        'role'             => $author['role'],
                        'rights_type'      => $author['rights_type'] ?? null,
                    ]);
                }
            }

            return $song;
        });

        return redirect()->route('tracks.index')->with('success', 'Трек добавлен');
    }

    public function show(Song $song)
    {
        $song->load(['artists', 'genre', 'label', 'songAuthors.artist']);

        return Inertia::render('Tracks/Show', [
            'song' => [
                'id'            => $song->id,
                'title'         => $song->title,
                'lyrics'        => $song->lyrics,
                'status'        => $song->status,
                'release_date'  => $song->release_date,
                'genre'         => $song->genre,
                'label'         => $song->label,
                'artists'       => $song->artists,
                'mp3_path'      => $song->mp3_path,
                'wav_path'      => $song->wav_path,
                'cover_path'    => $song->cover_path,
                'mp3_url'       => $song->mp3_path ? Storage::disk('public')->url($song->mp3_path) : null,
                'wav_url'       => $song->wav_path ? Storage::disk('public')->url($song->wav_path) : null,
                'cover_url'     => $song->cover_path ? Storage::disk('public')->url($song->cover_path) : '/images/default-cover.jpg',
                'song_authors'  => $song->songAuthors->map(fn ($sa) => [
                    'id'               => $sa->id,
                    'artist_id'        => $sa->artist_id,
                    'share_percentage' => $sa->share_percentage,
                    'role'             => $sa->role instanceof \BackedEnum ? $sa->role->value : $sa->role,
                    'rights_type'      => $sa->rights_type instanceof \BackedEnum ? $sa->rights_type->value : $sa->rights_type,
                    'artist'           => $sa->artist ? ['id' => $sa->artist->id, 'name' => $sa->artist->name] : null,
                ]),
                'created_at'    => $song->created_at,
                'updated_at'    => $song->updated_at,
            ]
        ]);
    }

    public function update(Request $request, Song $song)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'lyrics'      => 'nullable|string',
            'written_at'  => 'nullable|date',
            'released_at' => 'nullable|date',
            'label_id'    => 'nullable|exists:labels,id',
            'genre_id'    => 'nullable|exists:genres,id',
            'isrc'        => 'nullable|string|max:20',
            'mp3'         => 'nullable|file|mimes:mp3|max:51200',
            'wav'         => 'nullable|file|mimes:wav|max:102400',
        ]);

        $song->fill($validated);
        $this->storeSongFiles($song, $request, false);
        $song->save();

        return back()->with('success', 'Трек обновлён');
    }

    public function destroy(Song $song)
    {
        if ($song->mp3_path) Storage::disk('public')->delete($song->mp3_path);
        if ($song->wav_path) Storage::disk('public')->delete($song->wav_path);
        $song->delete();

        return redirect()->route('tracks.index')->with('success', 'Трек удалён');
    }

    protected function storeSongFiles(Song $song, Request $request, bool $required = true): void
    {
        if ($request->hasFile('mp3')) {
            if ($song->mp3_path) Storage::disk('public')->delete($song->mp3_path);
            $song->mp3_path = $request->file('mp3')->store('songs/audio', 'public');
        }

        if ($request->hasFile('wav')) {
            if ($song->wav_path) Storage::disk('public')->delete($song->wav_path);
            $song->wav_path = $request->file('wav')->store('songs/audio', 'public');
        }
    }
}