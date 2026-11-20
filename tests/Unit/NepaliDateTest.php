<?php

namespace Tests\Unit;

use App\Support\NepaliDate;
use PHPUnit\Framework\TestCase;

final class NepaliDateTest extends TestCase
{
    public function test_current_date_maps_to_the_correct_nepali_date(): void
    {
        self::assertSame('2083-08-04', NepaliDate::adToBsString('2026-11-20'));
    }

    public function test_current_nepali_date_maps_back_to_the_same_english_date(): void
    {
        self::assertSame('2026-11-20', NepaliDate::bsToAdString(2083, 8, 4));
    }
}
