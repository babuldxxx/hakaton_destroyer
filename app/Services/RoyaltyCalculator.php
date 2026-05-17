<?php

namespace App\Services;

class RoyaltyCalculator
{
    /**
     * Распределяет общий доход между лейблом и авторами.
     *
     * @param float $totalAmount  общая сумма дохода
     * @param array $authors      элементы с ключами 'artist_id' и 'share_percentage'
     * @param float $labelSharePercent  доля лейбла в процентах (0-100)
     * @return array [
     *   'label_share' => float,
     *   'authors_shares' => [ ['artist_id' => int, 'amount' => float], ... ]
     * ]
     */
    public function calculate(float $totalAmount, array $authors, float $labelSharePercent = 0): array
    {
        $labelShare = round($totalAmount * $labelSharePercent / 100, 2);
        $remaining = $totalAmount - $labelShare;

        $authorShares = [];
        foreach ($authors as $author) {
            $share = round($remaining * $author['share_percentage'] / 100, 2);
            $authorShares[] = [
                'artist_id' => $author['artist_id'],
                'amount' => $share,
            ];
        }

        $sum = $labelShare + array_sum(array_column($authorShares, 'amount'));
        if (abs($sum - $totalAmount) > 0.01) {
            $difference = round($totalAmount - $sum, 2);
            if (!empty($authorShares)) {
                $authorShares[array_key_last($authorShares)]['amount'] += $difference;
            } else {
                $labelShare += $difference;
            }
        }

        return [
            'label_share' => $labelShare,
            'authors_shares' => $authorShares,
        ];
    }
}
