<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Period;

use LibertJeremy\DateHelpers\Period\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function testFromDates1(): void
    {
        $datePeriod = Formatter::fromDates(new \DateTime('2017-12-01 08:11:09'), new \DateTime('2017-12-17 12:23:34'));

        self::assertEquals(new \DatePeriod(new \DateTime('2017-12-01 08:11:09'), new \DateInterval('P1D'), new \DateTime('2017-12-17 12:23:34')), $datePeriod);
    }

    public function testFromDates2(): void
    {
        $datePeriod = Formatter::fromDates(new \DateTime('2017-12-01 08:11:09'), new \DateTime('2017-12-17 12:23:34'), true);

        self::assertEquals(new \DatePeriod(new \DateTime('2017-12-01 00:00:00'), new \DateInterval('P1D'), new \DateTime('2017-12-17 23:59:59')), $datePeriod);
    }

    public function testFromDates3(): void
    {
        $date = new \DateTime('2019-08-03');

        $datePeriod = Formatter::fromDates($date, $date, true);

        self::assertEquals(new \DatePeriod(new \DateTime('2019-08-03 00:00:00'), new \DateInterval('P1D'), new \DateTime('2019-08-03 23:59:59')), $datePeriod);
    }

    public function testMonthByMonthNumber1(): void
    {
        $month = Formatter::monthByMonthNumber(9, 2006);

        self::assertEquals(new \DateTime('2006-09-01 00:00:00'), $month->getStartDate());
        self::assertEquals(new \DateTime('2006-09-30 23:59:59'), $month->getEndDate());
    }

    public function testMonthByMonthNumber2(): void
    {
        $month = Formatter::monthByMonthNumber(4, 2021);

        self::assertEquals(new \DateTime('2021-04-01 00:00:00'), $month->getStartDate());
        self::assertEquals(new \DateTime('2021-04-30 23:59:59'), $month->getEndDate());
    }

    public function testMonthByMonthNumber3(): void
    {
        $month = Formatter::monthByMonthNumber(9, 2006);

        self::assertEquals(new \DateTime('2006-09-01 00:00:00'), $month->getStartDate());
        self::assertEquals(new \DateTime('2006-09-30 23:59:59'), $month->getEndDate());
    }

    public function testMonthByMonthNumber4(): void
    {
        self::assertSame('20210101000000', ($monthFromMonthNumber = Formatter::monthByMonthNumber(1, 2021))->getStartDate()->format('YmdHis'));
        self::assertSame('20210131235959', $monthFromMonthNumber->getEndDate()->format('YmdHis'));
    }

    public function testMonthByMonthNumber5(): void
    {
        $currentYear = (new \DateTime())->format('Y');

        self::assertSame($currentYear.'0101000000', ($monthFromMonthNumber = Formatter::monthByMonthNumber(1, (int) $currentYear))->getStartDate()->format('YmdHis'));
        self::assertSame($currentYear.'0131235959', $monthFromMonthNumber->getEndDate()->format('YmdHis'));
    }

    public function testMonthByMonthNumber6(): void
    {
        self::assertSame('20240201000000', ($monthFromMonthNumber = Formatter::monthByMonthNumber(2, 2024))->getStartDate()->format('YmdHis'));
        self::assertSame('20240229235959', $monthFromMonthNumber->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonth1(): void
    {
        $datePeriods = [
            Formatter::fromDates(new \DateTime('2024-01-01'), new \DateTime('2024-01-22'), true),
            Formatter::fromDates(new \DateTime('2024-01-23'), new \DateTime('2024-07-22'), true),
            Formatter::fromDates(new \DateTime('2024-07-23'), new \DateTime('2024-12-31'), true),
        ];

        self::assertEquals([
            Formatter::fromDates(new \DateTime('2024-01-01'), new \DateTime('2024-01-22'), true),
            Formatter::fromDates(new \DateTime('2024-01-23'), new \DateTime('2024-01-31'), true),
            Formatter::monthByMonthNumber(2, 2024),
            Formatter::monthByMonthNumber(3, 2024),
            Formatter::monthByMonthNumber(4, 2024),
            Formatter::monthByMonthNumber(5, 2024),
            Formatter::monthByMonthNumber(6, 2024),
            Formatter::fromDates(new \DateTime('2024-07-01'), new \DateTime('2024-07-22'), true),
            Formatter::fromDates(new \DateTime('2024-07-23'), new \DateTime('2024-07-31'), true),
            Formatter::monthByMonthNumber(8, 2024),
            Formatter::monthByMonthNumber(9, 2024),
            Formatter::monthByMonthNumber(10, 2024),
            Formatter::monthByMonthNumber(11, 2024),
            Formatter::monthByMonthNumber(12, 2024),
        ], Formatter::splitPeriodsByMonth($datePeriods));
    }

    public function testSplitPeriodsByMonth2(): void
    {
        $datePeriods = [
            Formatter::fromDates(new \DateTime('2024-01-01'), new \DateTime('2024-02-29'), true),
            Formatter::fromDates(new \DateTime('2024-03-01'), new \DateTime('2024-04-30'), true),
        ];

        self::assertEquals([
            Formatter::monthByMonthNumber(1, 2024),
            Formatter::monthByMonthNumber(2, 2024),
            Formatter::monthByMonthNumber(3, 2024),
            Formatter::monthByMonthNumber(4, 2024),
        ], Formatter::splitPeriodsByMonth($datePeriods));
    }

    public function testSplitPeriodsByMonth3(): void
    {
        $datePeriods = [
            Formatter::fromDates(new \DateTime('2024-01-01'), new \DateTime('2024-02-29'), true),
        ];

        self::assertEquals([
            Formatter::monthByMonthNumber(1, 2024),
            Formatter::monthByMonthNumber(2, 2024),
        ], Formatter::splitPeriodsByMonth($datePeriods));
    }

    public function testSplitPeriodsByMonth4(): void
    {
        $datePeriods = [
            Formatter::monthByMonthNumber(1, 2024),
        ];

        self::assertEquals([
            Formatter::monthByMonthNumber(1, 2024),
        ], Formatter::splitPeriodsByMonth($datePeriods));
    }

    public function testSplitPeriodsByMonth5(): void
    {
        $datePeriods = [];

        self::assertEquals([
        ], Formatter::splitPeriodsByMonth($datePeriods));
    }

    public function testSplitPeriodsByMonth6(): void
    {
        $datePeriods = [
            Formatter::fromDates(new \DateTime('2024-01-10'), new \DateTime('2024-01-15'), true),
        ];

        self::assertEquals([
            Formatter::fromDates(new \DateTime('2024-01-10'), new \DateTime('2024-01-15'), true)
        ], Formatter::splitPeriodsByMonth($datePeriods));
    }
}
