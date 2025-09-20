<?php

namespace App\Support;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final class NepaliDate
{
    /** Nepali epoch year (BS) */
    private const EPOCH_YEAR = 2000;

    /** AD epoch (UTC) corresponding to BS 2000-01-01 */
    private const BEGIN_AD_Y = 1943;
    private const BEGIN_AD_M = 4;  // April (1-based for PHP)
    private const BEGIN_AD_D = 13; // 13th

    /** Boundaries (same as the JS lib) */
    private const MIN_DAY = 1;
    private const MAX_DAY = 33238; // computed from the mapping

    /** English + Nepali digits and month/day names (for formatting) */
    private const FORMAT = [
        'en' => [
            'dayShort'  => ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
            'dayLong'   => ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
            'monthShort'=> ['Bai','Jes','Asa','Shr','Bhd','Asw','Kar','Man','Pou','Mag','Fal','Cha'],
            'monthLong' => ['Baisakh','Jestha','Asar','Shrawan','Bhadra','Aswin','Kartik','Mangsir','Poush','Magh','Falgun','Chaitra'],
            'digits'    => ['0','1','2','3','4','5','6','7','8','9'],
        ],
        'np' => [
            'dayShort'  => ['आइत','सोम','मंगल','बुध','বिहি','शुक्र','शनि'],
            'dayLong'   => ['आइतबार','सोमबार','मंगलबार','बुधबार','बिहिबार','शुक्रबार','शनिबार'],
            'monthShort'=> ['बै','जे','अ','श्रा','भा','आ','का','मं','पौ','मा','फा','चै'],
            'monthLong' => ['बैशाख','जेठ','असार','श्रावण','भाद्र','आश्विन','कार्तिक','मंसिर','पौष','माघ','फाल्गुण','चैत्र'],
            'digits'    => ['०','१','२','३','४','५','६','७','८','९'],
        ],
    ];

    /**
     * Days in each month for each BS year from 2000..2090 (12 months each, 0-based index: Baisakh..Chaitra)
     * Ported from your JS mapping.
     */
    private const YEAR_MONTH_DAYS = [
        [30,32,31,32,31,30,30,30,29,30,29,31], // 2000
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [30,32,31,32,31,30,30,30,29,30,29,31], // 2004
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,31,31,32,31,31,29,30,30,29,29,31],
        [31,31,32,31,31,31,30,29,30,29,30,30], // 2009
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,31,31,32,31,31,29,30,30,29,30,30],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30], // 2014
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,31,31,32,31,31,29,30,30,29,30,30],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,30,29,31], // 2019
        [31,31,31,32,31,31,30,29,30,29,30,30],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,30],
        [31,32,31,32,31,30,30,30,29,30,29,31],
        [31,31,31,32,31,31,30,29,30,29,30,30], // 2024
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [30,32,31,32,31,30,30,30,29,30,29,31],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,31,32,30,30,29,30,29,30,30], // 2029
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [30,32,31,32,31,30,30,30,29,30,29,31],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31], // 2034
        [30,32,31,32,31,31,29,30,30,29,29,31],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,31,31,32,31,31,29,30,30,29,30,30], // 2039
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,31,31,32,31,31,29,30,30,29,30,30],
        [31,31,32,31,31,31,30,29,30,29,30,30], // 2044
        [31,32,31,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,30,29,31],
        [31,31,31,32,31,31,30,29,30,29,30,30],
        [31,31,32,31,32,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31], // 2049
        [30,32,31,32,31,30,30,30,29,30,29,31],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [30,32,31,32,31,31,29,30,30,29,29,31], // 2054
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,31,31,32,31,31,29,30,30,29,30,30],
        [31,31,32,31,31,31,30,29,30,29,30,30], // 2059
        [31,32,31,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [30,32,31,32,31,30,30,30,29,30,29,31],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,31,32,30,30,29,30,29,30,30], // 2064
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [30,32,31,32,31,31,29,30,30,29,29,31],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31], // 2069
        [31,31,31,32,31,31,29,30,30,29,30,30],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,30,29,31],
        [31,31,31,32,31,31,30,29,30,29,30,30], // 2074
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,31,31,32,31,31,29,30,30,29,30,30],
        [31,31,32,31,31,31,30,29,30,29,30,30], // 2079
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,32,31,32,31,30,30,30,29,29,30,30],
        [31,31,32,31,32,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31], // 2084
        [30,32,31,32,31,31,29,30,30,29,29,31],
        [31,31,32,31,31,31,30,29,30,29,30,30],
        [31,31,32,32,31,30,30,29,30,29,30,30],
        [31,32,31,32,31,30,30,30,29,29,30,31],
        [31,31,31,32,31,31,29,30,30,29,30,30], // 2089
        [31,31,32,31,31,31,30,29,30,29,30,30], // 2090
    ];

    /** computed at runtime */
    private static ?array $yearOffsets = null;   // days before each year
    private static ?array $yearTotals  = null;   // sum of months for each year
    private static ?array $monthPrefix = null;   // per-year prefix sums for months

    /** ---------- Public API ---------- */

    /**
     * Convert AD 'YYYY-MM-DD' (or DateTimeInterface) to BS string 'YYYY-MM-DD'.
     * $lang = 'en' (Latin digits) or 'np' (Nepali digits).
     */
    public static function adToBsString($ad, string $lang = 'en'): string
    {
        $dt = self::toDateUTC($ad);
        [$bsY, $bsM0, $bsD, $dow] = self::convertToBS($dt);
        $out = sprintf('%04d-%02d-%02d', $bsY, $bsM0 + 1, $bsD);
        return $lang === 'np' ? self::mapDigits($out, 'np') : $out;
    }

    /**
     * Convert BS (year, month 1-12, day) to AD (returns 'YYYY-MM-DD').
     */
    public static function bsToAdString(int $bsYear, int $bsMonth1, int $bsDay): string
    {
        [$adY, $adM0, $adD, $dow] = self::convertToAD($bsYear, $bsMonth1 - 1, $bsDay);
        return sprintf('%04d-%02d-%02d', $adY, $adM0 + 1, $adD);
    }

    /**
     * Format helper similar to JS: e.g. formatBS(2082,5,4,'YYYY-MM-DD','np')
     * (month is 1-12).
     */
    public static function formatBS(int $year, int $month1, int $day, string $format = 'YYYY-MM-DD', string $lang = 'en'): string
    {
        $bs = ['year'=>$year, 'month'=>$month1-1, 'date'=>$day, 'day'=>0];
        return self::format($bs, $format, $lang);
    }

    /** ---------- Internals (ported from JS) ---------- */

    private static function init(): void
    {
        if (self::$yearOffsets !== null) return;

        self::$yearTotals = [];
        self::$yearOffsets = [];
        self::$monthPrefix = [];

        $offset = 0;
        foreach (self::YEAR_MONTH_DAYS as $yIndex => $months) {
            $yearTotal = array_sum($months);
            self::$yearTotals[$yIndex]  = $yearTotal;
            self::$yearOffsets[$yIndex] = $offset;

            // prefix sums for months: prefix[i] = sum of month days before i
            $pref = [0];
            $s = 0;
            for ($i=0; $i<12; $i++) {
                $pref[$i] = $s;
                $s += $months[$i];
            }
            self::$monthPrefix[$yIndex] = $pref;

            $offset += $yearTotal;
        }

        // Safety (should equal 33238)
        if ($offset !== self::MAX_DAY) {
            throw new InvalidArgumentException('Invalid constant initialization for Nepali Date (sum mismatch).');
        }
    }

    private static function convertToBS(DateTimeImmutable $ad): array
    {
        self::init();

        $daysPassed = self::findPassedDaysAD((int)$ad->format('Y'), (int)$ad->format('n') - 1, (int)$ad->format('j'));
        $bs = self::mapDaysToDate($daysPassed);
        $dow = (int)$ad->format('w'); // day-of-week from AD

        return [$bs['year'], $bs['month'], $bs['date'], $dow];
    }

    private static function convertToAD(int $bsYear, int $bsMonth0, int $bsDay): array
    {
        self::init();

        $daysPassed = self::findPassedDays($bsYear, $bsMonth0, $bsDay);
        $ad = self::mapDaysToDateAD($daysPassed);

        return [$ad['year'], $ad['month'], $ad['date'], $ad['day']];
    }

    private static function findPassedDays(int $year, int $month0, int $date): int
    {
        try {
            $yIndex = $year - self::EPOCH_YEAR;
            $extraYear  = intdiv($month0, 12);
            $extraMonth = (($month0 % 12) + 12) % 12;

            $targetY = $yIndex + $extraYear;
            $days = self::$yearOffsets[$targetY] + self::$monthPrefix[$targetY][$extraMonth] + $date;

            if ($days < self::MIN_DAY || $days > self::MAX_DAY) {
                throw new InvalidArgumentException();
            }
            return $days;
        } catch (\Throwable $e) {
            throw new InvalidArgumentException("The date doesn't fall within 2000/01/01 - 2090/12/30");
        }
    }

    private static function mapDaysToDate(int $days): array
    {
        if ($days < self::MIN_DAY || $days > self::MAX_DAY) {
            throw new InvalidArgumentException("Epoch diff out of bounds");
        }

        // Find BS year index: offset < days <= offset + total
        $yIndex = null;
        foreach (self::$yearOffsets as $i => $off) {
            if ($days > $off && $days <= $off + self::$yearTotals[$i]) {
                $yIndex = $i; break;
            }
        }

        $rem = $days - self::$yearOffsets[$yIndex];

        // Find month index: prefix < rem <= prefix + monthDays
        $mIndex = null;
        $months = self::YEAR_MONTH_DAYS[$yIndex];
        $pref   = self::$monthPrefix[$yIndex];
        for ($i=0; $i<12; $i++) {
            $mDays = $months[$i];
            if ($rem > $pref[$i] && $rem <= $pref[$i] + $mDays) {
                $mIndex = $i; break;
            }
        }

        $date = $rem - $pref[$mIndex];
        return [
            'year'  => self::EPOCH_YEAR + $yIndex,
            'month' => $mIndex, // 0-based
            'date'  => $date,
        ];
    }

    private static function findPassedDaysAD(int $y, int $m0, int $d): int
    {
        $epoch = self::makeUTC(self::BEGIN_AD_Y, self::BEGIN_AD_M, self::BEGIN_AD_D);
        $cur   = self::makeUTC($y, $m0 + 1, $d);
        $diff  = abs($cur->getTimestamp() - $epoch->getTimestamp());
        return (int)ceil($diff / 86400);
    }

    private static function mapDaysToDateAD(int $days): array
    {
        $epoch = self::makeUTC(self::BEGIN_AD_Y, self::BEGIN_AD_M, self::BEGIN_AD_D + $days);
        return [
            'year'  => (int)$epoch->format('Y'),
            'month' => (int)$epoch->format('n') - 1, // 0-based
            'date'  => (int)$epoch->format('j'),
            'day'   => (int)$epoch->format('w'),
        ];
    }

    private static function toDateUTC($ad): DateTimeImmutable
    {
        if ($ad instanceof \DateTimeInterface) {
            return new DateTimeImmutable($ad->format('Y-m-d'), new DateTimeZone('UTC'));
        }
        // expects 'YYYY-MM-DD' or similar parseable string
        $dt = DateTimeImmutable::createFromFormat('!Y-m-d', (string)$ad, new DateTimeZone('UTC'));
        if (!$dt) {
            // try more forgiving parse
            $dt = new DateTimeImmutable((string)$ad, new DateTimeZone('UTC'));
        }
        return $dt;
    }

    /** Basic formatter (supports subset used by the JS lib) */
    private static function format(array $bs, string $fmt, string $lang): string
    {
        $L = self::FORMAT[$lang] ?? self::FORMAT['en'];
        $rep = function(string $s) use ($L) {
            return implode('', array_map(fn($ch) => $L['digits'][(int)$ch] ?? $ch, str_split($s)));
        };

        $out = preg_replace_callback('/((\\\\[MDYd])|D{1,2}|M{1,4}|Y{2,4}|d{1,3})/', function($m) use ($bs, $L, $rep) {
            $match = $m[0];
            switch ($match) {
                case 'D':  return $rep((string)$bs['date']);
                case 'DD': return $rep(str_pad((string)$bs['date'], 2, '0', STR_PAD_LEFT));
                case 'M':  return $rep((string)($bs['month'] + 1));
                case 'MM': return $rep(str_pad((string)($bs['month'] + 1), 2, '0', STR_PAD_LEFT));
                case 'MMM':return $L['monthShort'][$bs['month']];
                case 'MMMM':return $L['monthLong'][$bs['month']];
                case 'YY': return $rep(substr((string)$bs['year'], -2));
                case 'YYY':return $rep(substr((string)$bs['year'], -3));
                case 'YYYY':return $rep((string)$bs['year']);
                case 'd':  return $rep((string)($bs['day'] ?? 0));
                case 'dd': return $L['dayShort'][$bs['day'] ?? 0];
                case 'ddd':return $L['dayLong'][$bs['day'] ?? 0];
                default:   return ltrim($match, '\\'); // escaped char
            }
        }, $fmt);
        return str_replace('\\', '', $out);
    }

    private static function mapDigits(string $s, string $lang): string
    {
        $digits = self::FORMAT[$lang]['digits'] ?? self::FORMAT['en']['digits'];
        $map = ['0'=>$digits[0],'1'=>$digits[1],'2'=>$digits[2],'3'=>$digits[3],'4'=>$digits[4],
                '5'=>$digits[5],'6'=>$digits[6],'7'=>$digits[7],'8'=>$digits[8],'9'=>$digits[9]];
        return strtr($s, $map);
    }

    private static function makeUTC(int $y, int $m1, int $d): DateTimeImmutable
    {
        // '!Y-m-d' disables time parsing, and we set UTC to avoid TZ shifts
        $s = sprintf('%04d-%02d-%02d', $y, $m1, $d);
        return DateTimeImmutable::createFromFormat('!Y-m-d', $s, new DateTimeZone('UTC'));
    }
}
