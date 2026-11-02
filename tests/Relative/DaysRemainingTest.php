<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Relative;

use LibertJeremy\DateHelpers\Relative\DaysRemaining;
use PHPUnit\Framework\TestCase;

class DaysRemainingTest extends TestCase
{
    public function testSameDayIsZeroWhateverTheHours(): void
    {
        self::assertSame(0, DaysRemaining::between($this->lux('2026-11-04 23:59'), $this->lux('2026-11-04 00:01')));
    }

    public function testDeadlineTomorrowEarlyMorningIsOneDayEvenLessThanTwentyFourHoursAway(): void
    {
        self::assertSame(1, DaysRemaining::between($this->lux('2026-11-05 00:30'), $this->lux('2026-11-04 23:30')));
    }

    public function testDeadlineInTenDays(): void
    {
        self::assertSame(10, DaysRemaining::between($this->lux('2026-11-14 08:00'), $this->lux('2026-11-04 18:00')));
    }

    public function testPastDateIsNegative(): void
    {
        self::assertSame(-3, DaysRemaining::between($this->lux('2026-11-01 12:00'), $this->lux('2026-11-04 09:00')));
    }

    public function testYesterdayLateEveningIsMinusOne(): void
    {
        self::assertSame(-1, DaysRemaining::between($this->lux('2026-11-03 23:59'), $this->lux('2026-11-04 00:01')));
    }

    public function testAcrossSpringForwardTransitionEveryCalendarDayCountsOnce(): void
    {
        self::assertSame(2, DaysRemaining::between($this->lux('2026-03-30 00:00'), $this->lux('2026-03-28 12:00')));
    }

    public function testSpringForwardDayIsTwentyThreeHoursButStillOneCalendarDay(): void
    {
        self::assertSame(1, DaysRemaining::between($this->lux('2026-03-30 00:00'), $this->lux('2026-03-29 00:00')));
    }

    public function testSpringForwardNightLessThanOneDayAwayIsStillTomorrow(): void
    {
        self::assertSame(1, DaysRemaining::between($this->lux('2026-03-29 00:30'), $this->lux('2026-03-28 23:30')));
    }

    public function testAcrossFallBackTransitionEveryCalendarDayCountsOnce(): void
    {
        self::assertSame(2, DaysRemaining::between($this->lux('2026-10-26 00:00'), $this->lux('2026-10-24 12:00')));
    }

    public function testFallBackDayIsTwentyFiveHoursButStillOneCalendarDay(): void
    {
        self::assertSame(1, DaysRemaining::between($this->lux('2026-10-26 00:00'), $this->lux('2026-10-25 00:00')));
    }

    public function testFallBackDayEndStaysSameDay(): void
    {
        self::assertSame(0, DaysRemaining::between($this->lux('2026-10-25 23:59'), $this->lux('2026-10-25 00:00')));
    }

    public function testPastAcrossFallBackTransitionIsNegative(): void
    {
        self::assertSame(-2, DaysRemaining::between($this->lux('2026-10-24 12:00'), $this->lux('2026-10-26 00:00')));
    }

    public function testDateInAnotherTimezoneIsReadInTheTimezoneOfTheReference(): void
    {
        $date = new \DateTimeImmutable('2026-11-04 23:30:00', new \DateTimeZone('UTC'));

        self::assertSame(0, DaysRemaining::between($date, $this->lux('2026-11-05 10:00')));
    }

    public function testDateInAnotherTimezoneCanBeTheDayBefore(): void
    {
        $date = new \DateTimeImmutable('2026-11-04 23:30:00', new \DateTimeZone('UTC'));

        self::assertSame(1, DaysRemaining::between($date, $this->lux('2026-11-04 10:00')));
    }

    public function testAcrossYearEnd(): void
    {
        self::assertSame(2, DaysRemaining::between($this->lux('2027-01-01 10:00'), $this->lux('2026-12-30 22:00')));
    }

    private function lux(string $dateTime): \DateTimeImmutable
    {
        return new \DateTimeImmutable($dateTime, new \DateTimeZone('Europe/Luxembourg'));
    }
}
