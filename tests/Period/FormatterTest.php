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

    public function testSplitPeriodsByMonthEmptyInput(): void
    {
        $datePeriods = Formatter::splitPeriodsByMonth([]);
        self::assertSame([], $datePeriods);
    }

    public function testSplitPeriodsByMonthSingleMonthPartial(): void
    {
        $period = Formatter::fromDates(new \DateTime('2021-04-10 10:20:30'), new \DateTime('2021-04-20 14:00:00'));
        $datePeriods = Formatter::splitPeriodsByMonth([$period]);

        self::assertCount(3, $datePeriods);
        // Head: from 1st of month to first date
        self::assertSame('20210401000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20210410102029', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // Middle: actual period part
        self::assertSame('20210410102030', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20210420140000', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // Tail: from just after last date to end of month (inclusive at 23:59:59)
        self::assertSame('20210420140001', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20210430235959', $datePeriods[2]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthAcrossTwoMonths(): void
    {
        $period = Formatter::fromDates(new \DateTime('2021-04-10 10:20:30'), new \DateTime('2021-05-05 08:00:00'));
        $datePeriods = Formatter::splitPeriodsByMonth([$period]);

        self::assertCount(4, $datePeriods);
        // Head (April 1st to first date)
        self::assertSame('20210401000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20210410102029', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // April remainder
        self::assertSame('20210410102030', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20210430235959', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // May partial
        self::assertSame('20210501000000', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20210505080000', $datePeriods[2]->getEndDate()->format('YmdHis'));
        // Tail (rest of May to end of month)
        self::assertSame('20210505080001', $datePeriods[3]->getStartDate()->format('YmdHis'));
        self::assertSame('20210531235959', $datePeriods[3]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthAcrossThreeMonths(): void
    {
        $period = Formatter::fromDates(new \DateTime('2019-01-15 00:00:00'), new \DateTime('2019-03-10 00:00:00'));
        $datePeriods = Formatter::splitPeriodsByMonth([$period]);

        self::assertCount(5, $datePeriods);
        // Head (Jan 1st to Jan 15)
        self::assertSame('20190101000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20190114235959', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // Jan remainder
        self::assertSame('20190115000000', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20190131235959', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // February
        self::assertSame('20190201000000', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20190228235959', $datePeriods[2]->getEndDate()->format('YmdHis'));
        // March partial
        self::assertSame('20190301000000', $datePeriods[3]->getStartDate()->format('YmdHis'));
        self::assertSame('20190310000000', $datePeriods[3]->getEndDate()->format('YmdHis'));
        // Tail (rest of March)
        self::assertSame('20190310000001', $datePeriods[4]->getStartDate()->format('YmdHis'));
        self::assertSame('20190331235959', $datePeriods[4]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthUnionOfMultiplePeriods(): void
    {
        $p1 = Formatter::fromDates(new \DateTime('2019-02-10 12:00:00'), new \DateTime('2019-02-20 18:00:00'));
        $p2 = Formatter::fromDates(new \DateTime('2019-01-15 09:00:00'), new \DateTime('2019-01-25 10:00:00'));
        $p3 = Formatter::fromDates(new \DateTime('2019-03-05 08:30:00'), new \DateTime('2019-03-07 18:00:00'));

        $datePeriods = Formatter::splitPeriodsByMonth([$p1, $p2, $p3]);

        self::assertCount(5, $datePeriods);
        // Head (Jan 1st to first period start)
        self::assertSame('20190101000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20190115085959', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // Jan remainder
        self::assertSame('20190115090000', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20190131235959', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // February full
        self::assertSame('20190201000000', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20190228235959', $datePeriods[2]->getEndDate()->format('YmdHis'));
        // March partial
        self::assertSame('20190301000000', $datePeriods[3]->getStartDate()->format('YmdHis'));
        self::assertSame('20190307180000', $datePeriods[3]->getEndDate()->format('YmdHis'));
        // Tail (rest of March)
        self::assertSame('20190307180001', $datePeriods[4]->getStartDate()->format('YmdHis'));
        self::assertSame('20190331235959', $datePeriods[4]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthGapsAreIgnored(): void
    {
        $p1 = Formatter::fromDates(new \DateTime('2021-01-05 00:00:00'), new \DateTime('2021-01-07 00:00:00'));
        $p2 = Formatter::fromDates(new \DateTime('2021-03-10 00:00:00'), new \DateTime('2021-03-12 00:00:00'));

        $datePeriods = Formatter::splitPeriodsByMonth([$p1, $p2]);

        self::assertCount(5, $datePeriods);
        // Head (Jan 1st to first period start)
        self::assertSame('20210101000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20210104235959', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // Jan remainder
        self::assertSame('20210105000000', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20210131235959', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // February full
        self::assertSame('20210201000000', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20210228235959', $datePeriods[2]->getEndDate()->format('YmdHis'));
        // March partial
        self::assertSame('20210301000000', $datePeriods[3]->getStartDate()->format('YmdHis'));
        self::assertSame('20210312000000', $datePeriods[3]->getEndDate()->format('YmdHis'));
        // Tail (rest of March)
        self::assertSame('20210312000001', $datePeriods[4]->getStartDate()->format('YmdHis'));
        self::assertSame('20210331235959', $datePeriods[4]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthLeapYearFebruary(): void
    {
        $period = Formatter::fromDates(new \DateTime('2024-02-27 10:00:00'), new \DateTime('2024-03-02 12:00:00'));
        $datePeriods = Formatter::splitPeriodsByMonth([$period]);

        self::assertCount(4, $datePeriods);
        // Head (Feb 1st to first date)
        self::assertSame('20240201000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20240227095959', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // Feb remainder
        self::assertSame('20240227100000', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20240229235959', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // March partial
        self::assertSame('20240301000000', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20240302120000', $datePeriods[2]->getEndDate()->format('YmdHis'));
        // Tail (rest of March)
        self::assertSame('20240302120001', $datePeriods[3]->getStartDate()->format('YmdHis'));
        self::assertSame('20240331235959', $datePeriods[3]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthYearTransition(): void
    {
        $period = Formatter::fromDates(new \DateTime('2020-12-20 00:00:00'), new \DateTime('2021-01-10 00:00:00'));
        $datePeriods = Formatter::splitPeriodsByMonth([$period]);

        self::assertCount(4, $datePeriods);
        // Head (Dec 1st to Dec 20)
        self::assertSame('20201201000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20201219235959', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // Dec remainder
        self::assertSame('20201220000000', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20201231235959', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // Jan partial
        self::assertSame('20210101000000', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20210110000000', $datePeriods[2]->getEndDate()->format('YmdHis'));
        // Tail (rest of January)
        self::assertSame('20210110000001', $datePeriods[3]->getStartDate()->format('YmdHis'));
        self::assertSame('20210131235959', $datePeriods[3]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthNextMonthStartsAtMidnight(): void
    {
        $period = Formatter::fromDates(new \DateTime('2021-01-31 23:00:00'), new \DateTime('2021-02-02 10:00:00'));
        $datePeriods = Formatter::splitPeriodsByMonth([$period]);

        self::assertCount(4, $datePeriods);
        // Head (Jan 1st to Jan 31 23:00)
        self::assertSame('20210101000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20210131225959', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // Jan remainder (the actual crossing to Feb)
        self::assertSame('20210131230000', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20210131235959', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // February partial
        self::assertSame('20210201000000', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20210202100000', $datePeriods[2]->getEndDate()->format('YmdHis'));
        // Tail (rest of February)
        self::assertSame('20210202100001', $datePeriods[3]->getStartDate()->format('YmdHis'));
        self::assertSame('20210228235959', $datePeriods[3]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthSkipsOpenEndedPeriods(): void
    {
        $finite = Formatter::fromDates(new \DateTime('2021-04-10 10:20:30'), new \DateTime('2021-04-20 14:00:00'));
        $openEnded = new \DatePeriod(new \DateTime('2021-01-01 00:00:00'), new \DateInterval('P1D'), 10);

        $datePeriods = Formatter::splitPeriodsByMonth([$openEnded, $finite]);

        self::assertCount(3, $datePeriods);
        // Head (Apr 1st to first date)
        self::assertSame('20210401000000', $datePeriods[0]->getStartDate()->format('YmdHis'));
        self::assertSame('20210410102029', $datePeriods[0]->getEndDate()->format('YmdHis'));
        // Middle: the finite period itself
        self::assertSame('20210410102030', $datePeriods[1]->getStartDate()->format('YmdHis'));
        self::assertSame('20210420140000', $datePeriods[1]->getEndDate()->format('YmdHis'));
        // Tail (rest of April)
        self::assertSame('20210420140001', $datePeriods[2]->getStartDate()->format('YmdHis'));
        self::assertSame('20210430235959', $datePeriods[2]->getEndDate()->format('YmdHis'));
    }

    public function testSplitPeriodsByMonthOnlyOpenEndedReturnsEmpty(): void
    {
        $openEnded = new \DatePeriod(new \DateTime('2021-01-01 00:00:00'), new \DateInterval('P1D'), 10);
        $datePeriods = Formatter::splitPeriodsByMonth([$openEnded]);
        self::assertSame([], $datePeriods);
    }
}
