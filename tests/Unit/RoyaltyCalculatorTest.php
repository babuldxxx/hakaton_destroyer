<?php

namespace Tests\Unit;

use App\Services\RoyaltyCalculator;
use Tests\TestCase;

class RoyaltyCalculatorTest extends TestCase
{
    public function test_calculate_with_label_share()
    {
        $calc = new RoyaltyCalculator();
        $result = $calc->calculate(1000, [
            ['artist_id' => 1, 'share_percentage' => 60],
            ['artist_id' => 2, 'share_percentage' => 40],
        ], 10);

        $this->assertEquals(100, $result['label_share']);
        $this->assertEquals(540, $result['authors_shares'][0]['amount']);
        $this->assertEquals(360, $result['authors_shares'][1]['amount']);
    }

    public function test_distribution_without_label()
    {
        $calc = new RoyaltyCalculator();
        $result = $calc->calculate(500, [
            ['artist_id' => 1, 'share_percentage' => 100],
        ], 0);

        $this->assertEquals(0, $result['label_share']);
        $this->assertEquals(500, $result['authors_shares'][0]['amount']);
    }

    public function test_rounding_correction()
    {
        $calc = new RoyaltyCalculator();
        $result = $calc->calculate(100.01, [
            ['artist_id' => 1, 'share_percentage' => 60],
            ['artist_id' => 2, 'share_percentage' => 40],
        ], 0);

        $sum = $result['label_share'] + array_sum(array_column($result['authors_shares'], 'amount'));
        $this->assertEqualsWithDelta(100.01, $sum, 0.001);
    }
}
