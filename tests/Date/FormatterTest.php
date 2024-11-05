<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Date;

use LibertJeremy\DateHelpers\Date\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function testFirstDayOfMonthByDate1(): void
    {
        self::assertEquals(new \DateTime('2019-01-01'), Formatter::firstDayOfMonthByDate(new \DateTime('2019-01-30')));
    }

    public function testFirstDayOfMonthByDate2(): void
    {
        self::assertEquals(new \DateTime('2019-02-01'), Formatter::firstDayOfMonthByDate(new \DateTime('2019-02-27')));
    }

    public function testFirstDayOfMonthByDate3(): void
    {
        self::assertEquals(new \DateTime('2019-02-01'), Formatter::firstDayOfMonthByDate(new \DateTime('2019-02-01')));
    }

    public function testFirstDayOfWeekByDate1(): void
    {
        self::assertEquals(new \DateTime('2019-01-28'), Formatter::firstDayOfWeekByDate(new \DateTime('2019-01-30')));
    }

    public function testFirstDayOfWeekByDate2(): void
    {
        self::assertEquals(new \DateTime('2021-12-27'), Formatter::firstDayOfWeekByDate(new \DateTime('2021-12-30')));
    }

    public function testFirstDayOfWeekByDate3(): void
    {
        self::assertEquals(new \DateTime('2021-12-27'), Formatter::firstDayOfWeekByDate(new \DateTime('2021-12-27')));
    }

    public function testLastDayOfWeekByDate1(): void
    {
        self::assertEquals(new \DateTime('2019-02-03 23:59:59'), Formatter::lastDayOfWeekByDate(new \DateTime('2019-01-30')));
    }

    public function testLastDayOfWeekByDate2(): void
    {
        self::assertEquals(new \DateTime('2022-01-02 23:59:59'), Formatter::lastDayOfWeekByDate(new \DateTime('2021-12-30')));
    }

    public function testLastDayOfWeekByDate3(): void
    {
        self::assertEquals(new \DateTime('2022-01-02 23:59:59'), Formatter::lastDayOfWeekByDate(new \DateTime('2022-01-02')));
    }

    public function testLastDayOfMonthByDate1(): void
    {
        self::assertEquals(new \DateTime('2019-02-28 23:59:59'), Formatter::lastDayOfMonthByDate(new \DateTime('2019-02-24')));
    }

    public function testLastDayOfMonthByDate2(): void
    {
        self::assertEquals(new \DateTime('2019-01-31 23:59:59'), Formatter::lastDayOfMonthByDate(new \DateTime('2019-01-01')));
    }

    public function testLastDayOfMonthByDate3(): void
    {
        self::assertEquals(new \DateTime('2019-01-31 23:59:59'), Formatter::lastDayOfMonthByDate(new \DateTime('2019-01-31')));
    }

    public function testFromWeekNumber1(): void
    {
        self::assertEquals('2019-01-07', Formatter::fromWeekNumber(2, 2019)->format('Y-m-d'));
    }

    public function testFromWeekNumber2(): void
    {
        self::assertEquals('2018-02-19', Formatter::fromWeekNumber(8, 2018)->format('Y-m-d'));
    }

    public function testFromWeekNumber3(): void
    {
        self::assertEquals('2021-04-26', Formatter::fromWeekNumber(17, 2021)->format('Y-m-d'));
    }

    public function testFirstDayOfMonthByMonthNumber1(): void
    {
        self::assertEquals(new \DateTime('2019-02-01 00:00:00'), Formatter::firstDayOfMonthByMonthNumber(2, 2019));
    }

    public function testFirstDayOfMonthByMonthNumber2(): void
    {
        self::assertEquals(new \DateTime('2019-03-01 00:00:00'), Formatter::firstDayOfMonthByMonthNumber(3, 2019));
    }

    public function testLastDayOfMonthByMonthNumber1(): void
    {
        self::assertEquals(new \DateTime('2024-02-29 23:59:59'), Formatter::lastDayOfMonthByMonthNumber(2, 2024));
    }

    public function testFromDayAndMonthAndYearOrLastDayOfMonthIfNotExists1(): void
    {
        self::assertEquals(new \DateTime('2020-05-05 23:59:59'), Formatter::fromDayAndMonthAndYearOrLastDayOfMonthIfNotExists(5, 5, 2020));
    }

    public function testFromDayAndMonthAndYearOrLastDayOfMonthIfNotExists2(): void
    {
        self::assertEquals(new \DateTime('2020-02-29 23:59:59'), Formatter::fromDayAndMonthAndYearOrLastDayOfMonthIfNotExists(30, 2, 2020));
    }

    public function testFromDayAndMonthAndYearOrLastDayOfMonthIfNotExists3(): void
    {
        self::assertEquals(new \DateTime('2019-02-28 23:59:59'), Formatter::fromDayAndMonthAndYearOrLastDayOfMonthIfNotExists(30, 2, 2019));
    }

    public function testFromDayAndMonthAndYearOrLastDayOfMonthIfNotExists4(): void
    {
        self::assertEquals(new \DateTime('2022-02-28 23:59:59'), Formatter::fromDayAndMonthAndYearOrLastDayOfMonthIfNotExists(30, 2, 2022));
    }
}
