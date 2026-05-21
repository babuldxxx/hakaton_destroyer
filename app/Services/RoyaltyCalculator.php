<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RoyaltyCalculator
{
    public function distribute(Earning $earning): void
    {
        DB::transaction(function () use ($earning) {
            $earning->transactions()->where('status', 'pending')->delete();

            $gross = (float) $earning->gross_amount;
            $song = $earning->song()
                ->with(['songAuthors.artist.user', 'label'])
                ->first();

            if (! $song) {
                throw new InvalidArgumentException('Трек не найден.');
            }

            // --- ДОЛЯ ЛЕЙБЛА (из CSV или 0) ---
            $labelPercent = (float) ($earning->label_share_percent ?? 0);
            $labelAmount  = round($gross * ($labelPercent / 100), 2);
            $poolForAuthors = round($gross - $labelAmount, 2);

            if ($labelAmount > 0.01 && $song->label_id) {
                Transaction::create([
                    'earning_id'  => $earning->id,
                    'user_id'     => $song->label?->user_id ?? $earning->created_by,
                    'artist_id'   => null,
                    'amount'      => $labelAmount,
                    'type'        => 'label_share',
                    'status'      => 'pending',
                    'period'      => $earning->period,
                    'meta'        => [
                        'share_percent' => $labelPercent,
                        'from_gross'    => $gross,
                    ],
                ]);
            }

            // --- АВТОРЫ ---
            $authors = $song->songAuthors;

            if ($authors->isEmpty()) {
                if ($poolForAuthors > 0.01 && $song->label_id) {
                    Transaction::create([
                        'earning_id' => $earning->id,
                        'user_id'    => $song->label?->user_id ?? $earning->created_by,
                        'artist_id'  => null,
                        'amount'     => $poolForAuthors,
                        'type'       => 'unallocated',
                        'status'     => 'pending',
                        'period'     => $earning->period,
                        'meta'       => ['reason' => 'no_authors_attached', 'pool' => $poolForAuthors],
                    ]);
                }
                $earning->update(['status' => 'distributed']);
                return;
            }

            // Определяем доли: из CSV или из БД
            $csvShares = [];
            $useCsv = false;

            if (! empty($earning->artist_shares)) {
                $raw = $earning->artist_shares;
                $sep = str_contains($raw, '/') ? '/' : (str_contains($raw, ';') ? ';' : ',');
                $csvShares = array_values(array_filter(array_map('trim', explode($sep, $raw)), fn($v) => $v !== ''));

                if (count($csvShares) === $authors->count()) {
                    $useCsv = true;
                }
            }

            $totalPercent = $useCsv
                ? (float) array_sum($csvShares)
                : (float) $authors->sum('share_percentage');

            if ($totalPercent <= 0) {
                throw new InvalidArgumentException('Сумма долей авторов равна нулю.');
            }

            $distributed = 0.00;
            $lastIndex = $authors->count() - 1;

            foreach ($authors as $index => $songAuthor) {
                $rawPercent = $useCsv
                    ? (float) $csvShares[$index]
                    : (float) $songAuthor->share_percentage;

                if ($index === $lastIndex) {
                    $amount = round($poolForAuthors - $distributed, 2);
                } else {
                    $amount = round($poolForAuthors * ($rawPercent / $totalPercent), 2);
                    $distributed += $amount;
                }

                $recipientUserId = $songAuthor->artist?->user_id;

                if (! $recipientUserId) {
                    Transaction::create([
                        'earning_id' => $earning->id,
                        'user_id'    => $song->label?->user_id ?? $earning->created_by,
                        'artist_id'  => $songAuthor->artist_id,
                        'amount'     => $amount,
                        'type'       => 'unallocated',
                        'status'     => 'pending',
                        'period'     => $earning->period,
                        'meta'       => [
                            'reason'           => 'artist_without_user',
                            'song_author_id'   => $songAuthor->id,
                            'share_percentage' => $rawPercent,
                            'source'           => $useCsv ? 'csv' : 'db',
                        ],
                    ]);
                    continue;
                }

                Transaction::create([
                    'earning_id'  => $earning->id,
                    'user_id'     => $recipientUserId,
                    'artist_id'   => $songAuthor->artist_id,
                    'amount'      => $amount,
                    'type'        => 'author_royalty',
                    'status'      => 'pending',
                    'period'      => $earning->period,
                    'meta'        => [
                        'share_percentage'   => $rawPercent,
                        'normalized_total'   => $totalPercent,
                        'role'               => $songAuthor->role,
                        'pool'               => $poolForAuthors,
                        'source'             => $useCsv ? 'csv' : 'db',
                    ],
                ]);
            }

            $earning->update(['status' => 'distributed']);
        });
    }
}