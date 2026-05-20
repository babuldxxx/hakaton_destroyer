<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Earning;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\Song;
use App\Models\Transaction;
use App\Services\RoyaltyCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SongController extends Controller
{
    private function ensureLabel(): void
    {
        if (!auth()->user()->hasRole('label')) {
            abort(403, 'Только для лейбла.');
        }
    }

    public function index()
    {
        $songs = Song::with(['artists', 'genre', 'label', 'songAuthors.artist', 'platforms'])
            ->latest()
            ->paginate(20)
            ->through(fn (Song $song) => [
                'id'           => $song->id,
                'title'        => $song->title,
                'status'       => $song->status,
                'release_date' => $song->released_at?->toDateString(),
                'genre'        => $song->genre,
                'label'        => $song->label,
                'artists'      => $song->artists,
                'cover_url'    => $song->cover_path ? Storage::disk('public')->url($song->cover_path) : '/images/default-cover.jpg',
                'mp3_url'      => $song->mp3_path ? Storage::disk('public')->url($song->mp3_path) : null,
                'song_authors' => $song->songAuthors,
                'platforms'    => $song->platforms->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'slug' => $p->slug]),
            ]);

        return Inertia::render('Tracks/Index', ['tracks' => $songs]);
    }

    public function create()
    {
        $this->ensureLabel();

        return Inertia::render('Tracks/Create', [
            'genres'    => Genre::select('id', 'name')->get(),
            'artists'   => Artist::select('id', 'real_name', 'stage_name')->get(),
            'platforms' => Platform::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureLabel();

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'lyrics'        => 'nullable|string',
            'written_at'    => 'nullable|date',
            'released_at'   => 'nullable|date',
            'genre_id'      => 'nullable|exists:genres,id',
            'cover'         => 'nullable|image|max:5120',
            'mp3'           => 'nullable|file|mimes:mp3|max:51200',
            'wav'           => 'nullable|file|mimes:wav|max:512000',
            'platforms'     => 'nullable|array',
            'platforms.*'   => 'integer|exists:platforms,id',
            'authors'       => 'nullable|array',
            'authors.*.artist_id'        => 'required_with:authors|exists:artists,id',
            'authors.*.share_percentage' => 'required_with:authors|integer|min:0|max:100',
            'authors.*.role'             => 'required_with:authors|string|in:author,performer,producer',
            'authors.*.rights_type'      => 'required_with:authors|string|in:author_rights,related_rights',
        ]);

        $song = Song::create([
            'title'       => $validated['title'],
            'lyrics'      => $validated['lyrics'] ?? null,
            'written_at'  => $validated['written_at'] ?? null,
            'released_at' => $validated['released_at'] ?? null,
            'genre_id'    => $validated['genre_id'] ?? null,
            'user_id'     => auth()->id(),
        ]);

        if (!empty($validated['platforms'])) {
            $song->platforms()->sync($validated['platforms']);
        }

        if ($request->hasFile('cover')) {
            $song->cover_path = $request->file('cover')->store('covers', 'public');
        }
        if ($request->hasFile('mp3')) {
            $song->mp3_path = $request->file('mp3')->store('tracks/mp3', 'public');
        }
        if ($request->hasFile('wav')) {
            $song->wav_path = $request->file('wav')->store('tracks/wav', 'public');
        }
        $song->save();

        if (!empty($validated['authors'])) {
            foreach ($validated['authors'] as $author) {
                $song->songAuthors()->create([
                    'artist_id'        => $author['artist_id'],
                    'share_percentage' => $author['share_percentage'],
                    'role'             => $author['role'],
                    'rights_type'      => $author['rights_type'] ?? 'author_rights',
                ]);
            }
        }

        return redirect()->route('tracks.index')->with('success', 'Трек добавлен');
    }

    public function show(Song $song)
    {
        $song->load(['genre', 'platforms', 'songAuthors.artist', 'earnings.platform', 'earnings.transactions.artist']);

        $transactions = $song->earnings->flatMap->transactions->sortByDesc('created_at')->values();

        return Inertia::render('Tracks/Show', [
            'song' => [
                'id'             => $song->id,
                'title'          => $song->title,
                'lyrics'         => $song->lyrics,
                'genre'          => $song->genre?->name ?? '—',
                'release_date'   => $song->released_at?->toDateString() ?? '—',
                'written_at'     => $song->written_at?->toDateString() ?? null,
                'cover_url'      => $song->cover_path ? Storage::disk('public')->url($song->cover_path) : null,
                'mp3_url'        => $song->mp3_path ? Storage::disk('public')->url($song->mp3_path) : null,
                'wav_url'        => $song->wav_path ? Storage::disk('public')->url($song->wav_path) : null,
                'total_revenue'  => number_format($song->earnings->sum('gross_amount'), 2, '.', ''),
                'earnings_list'  => $song->earnings->map(fn ($e) => [
                    'id'                  => $e->id,
                    'platform'            => $e->platform?->name ?? '—',
                    'period'              => $e->period,
                    'gross_amount'        => $e->gross_amount,
                    'label_share_percent' => $e->label_share_percent,
                    'status'              => $e->status,
                ]),
                'transactions_list' => $transactions->map(fn ($t) => [
                    'id'        => $t->id,
                    'amount'    => $t->amount,
                    'type'      => $t->type,
                    'status'    => $t->status,
                    'period'    => $t->period,
                    'recipient' => $t->artist?->stage_name ?? $t->artist?->real_name ?? 'Лейбл',
                    'platform'  => $t->earning?->platform?->name ?? '—',
                    'meta'      => $t->meta,
                ]),
                'song_authors'   => $song->songAuthors->map(fn ($a) => [
                    'id'               => $a->id,
                    'share_percentage' => $a->share_percentage,
                    'role'             => $a->role,
                    'rights_type'      => $a->rights_type,
                    'artist'           => $a->artist ? [
                        'id'         => $a->artist->id,
                        'real_name'  => $a->artist->real_name,
                        'stage_name' => $a->artist->stage_name,
                    ] : null,
                ]),
            ],
            'platforms' => Platform::select('id', 'name')->get(),
        ]);
    }

    public function edit(Song $song)
    {
        $this->ensureLabel();

        $song->load(['platforms', 'songAuthors']);

        return Inertia::render('Tracks/Edit', [
            'song' => [
                'id'           => $song->id,
                'title'        => $song->title,
                'lyrics'       => $song->lyrics,
                'written_at'   => $song->written_at?->toDateString(),
                'released_at'  => $song->released_at?->toDateString(),
                'genre_id'     => $song->genre_id,
                'platform_ids' => $song->platforms->pluck('id'),
                'song_authors' => $song->songAuthors->map(fn ($a) => [
                    'artist_id'        => $a->artist_id,
                    'share_percentage' => $a->share_percentage,
                    'role'             => $a->role,
                    'rights_type'      => $a->rights_type,
                ]),
                'cover_url' => $song->cover_path ? Storage::disk('public')->url($song->cover_path) : null,
                'mp3_url'   => $song->mp3_path ? Storage::disk('public')->url($song->mp3_path) : null,
                'wav_url'   => $song->wav_path ? Storage::disk('public')->url($song->wav_path) : null,
            ],
            'genres'    => Genre::select('id', 'name')->get(),
            'artists'   => Artist::select('id', 'real_name', 'stage_name')->get(),
            'platforms' => Platform::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Song $song)
    {
        $this->ensureLabel();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'lyrics'      => 'nullable|string',
            'written_at'  => 'nullable|date',
            'released_at' => 'nullable|date',
            'genre_id'    => 'nullable|exists:genres,id',
            'cover'       => 'nullable|image|max:5120',
            'mp3'         => 'nullable|file|mimes:mp3|max:51200',
            'wav'         => 'nullable|file|mimes:wav|max:512000',
            'platforms'   => 'nullable|array',
            'platforms.*' => 'integer|exists:platforms,id',
            'authors'     => 'nullable|array',
            'authors.*.artist_id'        => 'required_with:authors|exists:artists,id',
            'authors.*.share_percentage' => 'required_with:authors|integer|min:0|max:100',
            'authors.*.role'             => 'required_with:authors|string|in:author,performer,producer',
            'authors.*.rights_type'      => 'required_with:authors|string|in:author_rights,related_rights',
        ]);

        $song->update([
            'title'       => $validated['title'],
            'lyrics'      => $validated['lyrics'] ?? null,
            'written_at'  => $validated['written_at'] ?? null,
            'released_at' => $validated['released_at'] ?? null,
            'genre_id'    => $validated['genre_id'] ?? null,
        ]);

        $song->platforms()->sync($request->input('platforms', []));

        if ($request->hasFile('cover')) {
            if ($song->cover_path) Storage::disk('public')->delete($song->cover_path);
            $song->cover_path = $request->file('cover')->store('covers', 'public');
        }
        if ($request->hasFile('mp3')) {
            if ($song->mp3_path) Storage::disk('public')->delete($song->mp3_path);
            $song->mp3_path = $request->file('mp3')->store('tracks/mp3', 'public');
        }
        if ($request->hasFile('wav')) {
            if ($song->wav_path) Storage::disk('public')->delete($song->wav_path);
            $song->wav_path = $request->file('wav')->store('tracks/wav', 'public');
        }
        $song->save();

        $song->songAuthors()->delete();
        if (!empty($validated['authors'])) {
            foreach ($validated['authors'] as $author) {
                $song->songAuthors()->create([
                    'artist_id'        => $author['artist_id'],
                    'share_percentage' => $author['share_percentage'],
                    'role'             => $author['role'],
                    'rights_type'      => $author['rights_type'] ?? 'author_rights',
                ]);
            }
        }

        return redirect()->route('tracks.index')->with('success', 'Трек обновлён');
    }

    public function destroy(Song $song)
    {
        $this->ensureLabel();

        if ($song->cover_path) Storage::disk('public')->delete($song->cover_path);
        if ($song->mp3_path)  Storage::disk('public')->delete($song->mp3_path);
        if ($song->wav_path)  Storage::disk('public')->delete($song->wav_path);

        $song->delete();

        return redirect()->route('tracks.index')->with('success', 'Трек удалён');
    }

    public function storeEarning(Request $request, Song $song)
    {
        $this->ensureLabel();

        $validated = $request->validate([
            'platform_id'         => 'required|exists:platforms,id',
            'gross_amount'        => 'required|numeric|min:0.01',
            'period'              => 'required|date_format:Y-m',
            'label_share_percent' => 'nullable|numeric|between:0,100',
        ]);

        $earning = Earning::create([
            'song_id'             => $song->id,
            'platform_id'         => $validated['platform_id'],
            'period'              => $validated['period'],
            'gross_amount'        => $validated['gross_amount'],
            'label_share_percent' => $validated['label_share_percent'] ?? 0,
            'created_by'          => auth()->id(),
            'status'              => 'pending',
        ]);

        app(RoyaltyCalculator::class)->distribute($earning);

        return back()->with('success', 'Доход добавлен и распределён между участниками.');
    }
}
