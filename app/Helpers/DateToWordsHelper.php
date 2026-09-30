<?php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Deterministic date-to-words, for alumni/verification letters that spell
 * out a date the way SJUT's own real letters do (e.g. "the Sixth day of
 * December in the year Two Thousand and Twenty Five") - see the real
 * sample letters this was built against
 * ([[project_gmsa_graduation_verification_slice]]). Pure PHP, no AI
 * involved at render time - the same input must always produce the exact
 * same output, which an LLM call could never guarantee.
 */
class DateToWordsHelper
{
    private const ONES = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen',
    ];

    private const TENS = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety',
    ];

    private const ORDINAL_ONES = [
        '', 'First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth',
        'Tenth', 'Eleventh', 'Twelfth', 'Thirteenth', 'Fourteenth', 'Fifteenth', 'Sixteenth',
        'Seventeenth', 'Eighteenth', 'Nineteenth',
    ];

    private const ORDINAL_TENS = [
        '', '', 'Twentieth', 'Thirtieth', 'Fortieth', 'Fiftieth', 'Sixtieth', 'Seventieth', 'Eightieth', 'Ninetieth',
    ];

    /**
     * "the Sixth day of December in the year Two Thousand and Twenty Five"
     */
    public static function fullDate($date, bool $includeWeekday = false): string
    {
        $carbon = self::parse($date);

        $prefix = $includeWeekday ? $carbon->format('l, ').'the ' : 'the ';

        return $prefix.self::ordinalDay((int) $carbon->format('j')).' day of '.$carbon->format('F').' in the year '.self::year((int) $carbon->format('Y'));
    }

    /**
     * A day number (1-31) spelled out as an ordinal word - "Sixth",
     * "Twenty-Third", "Thirty-First".
     */
    public static function ordinalDay(int $day): string
    {
        if ($day < 1 || $day > 31) {
            throw new \InvalidArgumentException("Day must be between 1 and 31, got {$day}.");
        }

        return self::ordinal($day);
    }

    /**
     * Any number 1-999 spelled out as an ordinal word - "Sixth",
     * "Twenty-Third", "Forty-Fifth Graduation Ceremony" territory. Wider
     * range than a calendar day needs, since this also backs a
     * congregation's own sequence number ("the Third Graduation
     * Ceremony"), which isn't bounded to 31.
     */
    public static function ordinal(int $number): string
    {
        if ($number < 1 || $number > 999) {
            throw new \InvalidArgumentException("Number must be between 1 and 999, got {$number}.");
        }

        if ($number < 20) {
            return self::ORDINAL_ONES[$number];
        }

        if ($number < 100) {
            $tens = intdiv($number, 10);
            $ones = $number % 10;

            return $ones === 0
                ? self::ORDINAL_TENS[$tens]
                : self::TENS[$tens].'-'.self::ORDINAL_ONES[$ones];
        }

        $hundreds = intdiv($number, 100);
        $rest = $number % 100;

        if ($rest === 0) {
            return self::ONES[$hundreds].' Hundredth';
        }

        return self::ONES[$hundreds].' Hundred and '.self::ordinal($rest);
    }

    /**
     * A 4-digit year (1900-2100) spelled out - "Two Thousand and Twenty
     * Five" (2025), "Nineteen Ninety Nine" (1999). Two different
     * conventions by design: years in the 2000s read naturally as
     * "Two Thousand and [rest]", but a pre-2000 year reads far more
     * naturally split as century-pair + rest ("Nineteen Ninety Nine"),
     * matching how these are actually spoken/written.
     */
    public static function year(int $year): string
    {
        if ($year < 1900 || $year > 2100) {
            throw new \InvalidArgumentException("Year must be between 1900 and 2100, got {$year}.");
        }

        if ($year === 2100) {
            return 'Two Thousand One Hundred';
        }

        if ($year > 2000) {
            return 'Two Thousand and '.self::cardinalUnder100($year - 2000);
        }

        if ($year === 2000) {
            return 'Two Thousand';
        }

        // 1900-1999: century pair + remainder read separately ("Nineteen
        // Ninety Nine"), a round century reads as "...Hundred" ("Nineteen
        // Hundred" for 1900) rather than a meaningless "...Zero".
        $century = intdiv($year, 100);
        $rest = $year % 100;

        return $rest === 0
            ? self::cardinalUnder100($century).' Hundred'
            : self::cardinalUnder100($century).' '.self::cardinalUnder100($rest);
    }

    /**
     * A number 0-99 spelled out in cardinal form - "Twenty Five", "Ninety
     * Nine", "Five".
     */
    private static function cardinalUnder100(int $number): string
    {
        if ($number < 20) {
            return self::ONES[$number] ?: 'Zero';
        }

        $tens = intdiv($number, 10);
        $ones = $number % 10;

        return trim(self::TENS[$tens].' '.self::ONES[$ones]);
    }

    private static function parse($date): CarbonInterface
    {
        return $date instanceof CarbonInterface ? $date : Carbon::parse($date);
    }
}
