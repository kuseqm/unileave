<?php

namespace Tests\Unit;

use App\Helpers\DateToWordsHelper;
use PHPUnit\Framework\TestCase;

class DateToWordsHelperTest extends TestCase
{
    public function test_ordinal_day_single_digits(): void
    {
        $this->assertSame('First', DateToWordsHelper::ordinalDay(1));
        $this->assertSame('Second', DateToWordsHelper::ordinalDay(2));
        $this->assertSame('Third', DateToWordsHelper::ordinalDay(3));
        $this->assertSame('Sixth', DateToWordsHelper::ordinalDay(6));
        $this->assertSame('Ninth', DateToWordsHelper::ordinalDay(9));
    }

    public function test_ordinal_day_teens(): void
    {
        $this->assertSame('Tenth', DateToWordsHelper::ordinalDay(10));
        $this->assertSame('Eleventh', DateToWordsHelper::ordinalDay(11));
        $this->assertSame('Nineteenth', DateToWordsHelper::ordinalDay(19));
    }

    public function test_ordinal_day_round_tens(): void
    {
        $this->assertSame('Twentieth', DateToWordsHelper::ordinalDay(20));
        $this->assertSame('Thirtieth', DateToWordsHelper::ordinalDay(30));
    }

    public function test_ordinal_day_compound(): void
    {
        $this->assertSame('Twenty-First', DateToWordsHelper::ordinalDay(21));
        $this->assertSame('Twenty-Third', DateToWordsHelper::ordinalDay(23));
        $this->assertSame('Thirty-First', DateToWordsHelper::ordinalDay(31));
    }

    public function test_ordinal_day_rejects_out_of_range(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DateToWordsHelper::ordinalDay(32);
    }

    public function test_ordinal_day_rejects_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DateToWordsHelper::ordinalDay(0);
    }

    public function test_year_2000s(): void
    {
        $this->assertSame('Two Thousand and Twenty Five', DateToWordsHelper::year(2025));
        $this->assertSame('Two Thousand and Thirteen', DateToWordsHelper::year(2013));
        $this->assertSame('Two Thousand and One', DateToWordsHelper::year(2001));
    }

    public function test_year_exactly_2000(): void
    {
        $this->assertSame('Two Thousand', DateToWordsHelper::year(2000));
    }

    public function test_year_1900s(): void
    {
        $this->assertSame('Nineteen Ninety Nine', DateToWordsHelper::year(1999));
        $this->assertSame('Nineteen Hundred', DateToWordsHelper::year(1900));
    }

    public function test_year_boundaries(): void
    {
        $this->assertSame('Two Thousand One Hundred', DateToWordsHelper::year(2100));
    }

    public function test_year_rejects_out_of_range(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DateToWordsHelper::year(1899);
    }

    public function test_full_date_without_weekday(): void
    {
        $this->assertSame(
            'the Sixth day of December in the year Two Thousand and Twenty Five',
            DateToWordsHelper::fullDate('2025-12-06')
        );
    }

    public function test_ordinal_beyond_31(): void
    {
        $this->assertSame('Forty-Fifth', DateToWordsHelper::ordinal(45));
        $this->assertSame('One Hundredth', DateToWordsHelper::ordinal(100));
        $this->assertSame('One Hundred and First', DateToWordsHelper::ordinal(101));
    }

    public function test_full_date_with_weekday(): void
    {
        $this->assertSame(
            'Saturday, the Twenty-Third day of November in the year Two Thousand and Thirteen',
            DateToWordsHelper::fullDate('2013-11-23', includeWeekday: true)
        );
    }
}
