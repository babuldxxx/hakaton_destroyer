<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RoyaltyCalculator
{
    /**
     * Распределить доход earning между участниками трека.
     * Вызывается после создания Earning (ручной ввод или CSV).
     */
    public function distribute(Earning $earning): void
    {
        DB::transaction(function () use ($earning) {
            // Удаляем старые неоплаченные транзакции по этому earning (для пересчёта)
            $earning->transactions()->where('status', 'pending')->delete();

            $gross = (float) $earning->gross_amount;
            $song = $earning->song()
                ->with(['songAuthors.artist.user', 'label']) // подгружаем авторов + их юзеров
                ->first();

            if (! $song) {
                throw new InvalidArgumentException('Трек не найден.');
            }

            // --- 1. ДОЛЯ ЛЕЙБЛА ---
            $labelPercent = (float) ($earning->label_share_percent ?? 0);
            $labelAmount = round($gross * ($labelPercent / 100), 2);
            $poolForAuthors = round($gross - $labelAmount, 2);

            // Транзакция лейблу (если есть доля и у трека указан лейбл)
            if ($labelAmount > 0.00 && $song->label_id) {
                $labelUserId = $song->label?->user_id ?? $song->label_id; // запасной вариант

                Transaction::create([
                    'earning_id'  => $earning->id,
                    'user_id'     => $labelUserId,
                    'artist_id'   => null, // лейбл не артист
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

            // --- 2. АВТОРЫ ТРЕКА (из song_authors) ---
            $authors = $song->songAuthors;

            if ($authors->isEmpty()) {
                // Нет авторов — остаток идёт "не распределённым" лейблу
                if ($poolForAuthors > 0.00 && $song->label_id) {
                    Transaction::create([
                        'earning_id' => $earning->id,
                        'user_id'    => $song->label?->user_id ?? $song->label_id,
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

            // Сумма всех долей (если в сумме не 100 — нормализуем пропорционально)
            $totalPercent = (float) $authors->sum('share_percentage');

            if ($totalPercent <= 0) {
                throw new InvalidArgumentException('Сумма долей авторов равна нулю.');
            }

            $distributed = 0.00;
            $lastIndex = $authors->count() - 1;

            foreach ($authors as $index => $songAuthor) {
                $rawPercent = (float) $songAuthor->share_percentage;

                // Последнему отдаём остаток копеек (чтобы не было ошибок округления)
                if ($index === $lastIndex) {
                    $amount = round($poolForAuthors - $distributed, 2);
                } else {
                    $amount = round($poolForAuthors * ($rawPercent / $totalPercent), 2);
                    $distributed += $amount;
                }

                // Кому платим: artist.user_id
                $recipientUserId = $songAuthor->artist?->user_id;

                if (! $recipientUserId) {
                    // Если у артиста нет привязки к юзеру — отправляем лейблу как unallocated
                    // или можно выбросить исключение. Выбери нужное поведение:
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
                        ],
                    ]);
                    continue;
                }

                // Маппим rights_type из SongAuthor в type транзакции
                $txType = $this->mapRightsType($songAuthor->rights_type);

                Transaction::create([
                    'earning_id'  => $earning->id,
                    'user_id'     => $recipientUserId,
                    'artist_id'   => $songAuthor->artist_id,
                    'amount'      => $amount,
                    'type'        => $txType,
                    'status'      => 'pending',
                    'period'      => $earning->period,
                    'meta'        => [
                        'share_percentage' => $rawPercent,
                        'normalized_total' => $totalPercent,
                        'rights_type'      => $songAuthor->rights_type?->value ?? $songAuthor->rights_type,
                        'role'             => $songAuthor->role?->value ?? $songAuthor->role,
                        'pool'             => $poolForAuthors,
                    ],
                ]);
            }

            // --- 3. ФИНАЛ ---
            $earning->update(['status' => 'distributed']);
        });
    }

    /**
     * Пересчитать earning (например, после редактирования авторов трека).
     */
    public function recalculate(Earning $earning): void
    {
        if ($earning->status === 'paid') {
            throw new InvalidArgumentException('Нельзя пересчитать уже выплаченный earning.');
        }

        $this->distribute($earning);
    }

    /**
     * Маппинг rights_type из SongAuthor в тип транзакции.
     */
    protected function mapRightsType(mixed $rightsType): string
    {
        $value = is_string($rightsType) ? $rightsType : ($rightsType?->value ?? 'author_rights');

        return match ($value) {
            'related_rights' => 'related_rights',
            'author_rights'  => 'author_rights',
            default          => 'author_rights',
        };
    }
}