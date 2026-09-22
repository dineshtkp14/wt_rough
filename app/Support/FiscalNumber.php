<?php

namespace App\Support;

use DateTimeInterface;

final class FiscalNumber
{
    public static function fiscalYearForDate($date): string
    {
        $bs = NepaliDate::adToBsString($date, 'en');
        [$year, $month] = array_map('intval', explode('-', $bs));
        $startYear = $month >= 4 ? $year : $year - 1;

        return sprintf('%04d/%02d', $startYear, ($startYear + 1) % 100);
    }

    public static function format(string $fiscalYear, int $sequence, string $prefix = ''): string
    {
        return ($prefix ? $prefix . '-' : '') . $fiscalYear . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
