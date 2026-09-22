<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

final class FiscalNumber
{
    /** First record date that uses the new fiscal display system. */
    public const DISPLAY_CUTOVER_AT = '2026-09-22 00:00:00';
    public const DISPLAY_MODE_CACHE_KEY = 'fiscal_display_for_users_enabled';

    public static function isFiscalDisplayEnabled(): bool
    {
        return (bool) Cache::get(self::DISPLAY_MODE_CACHE_KEY, true);
    }

    public static function shouldShowFiscalToCurrentUser(): bool
    {
        return self::isFiscalDisplayEnabled()
            && !(auth()->check() && auth()->user()->isAdmin());
    }

    public static function isAfterDisplayCutover($date): bool
    {
        if (!$date) {
            return false;
        }

        return Carbon::parse($date, 'Asia/Kathmandu')
            ->greaterThanOrEqualTo(Carbon::parse(self::DISPLAY_CUTOVER_AT, 'Asia/Kathmandu'));
    }

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
