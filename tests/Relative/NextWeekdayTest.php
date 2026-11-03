<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Relative;

use LibertJeremy\DateHelpers\Relative\NextWeekday;
use PHPUnit\Framework\TestCase;

/**
 * Jour de référence : mercredi 4 novembre 2026, 15 h 30 (Europe/Luxembourg).
 */
class NextWeekdayTest extends TestCase
{
    public function testLaterThisWeek(): void
    {
        self::assertSame('2026-11-06', NextWeekday::after($this->now(), 5)->format('Y-m-d'));
    }

    public function testEarlierWeekdayFallsNextWeek(): void
    {
        self::assertSame('2026-11-09', NextWeekday::after($this->now(), 1)->format('Y-m-d'));
    }

    public function testSameWeekdayExcludingTodayIsSevenDaysLater(): void
    {
        self::assertSame('2026-11-11', NextWeekday::after($this->now(), 3)->format('Y-m-d'));
    }

    public function testSameWeekdayIncludingTodayIsToday(): void
    {
        self::assertSame('2026-11-04', NextWeekday::after($this->now(), 3, true)->format('Y-m-d'));
    }

    public function testIncludeTodayDoesNotChangeAnotherWeekday(): void
    {
        self::assertSame('2026-11-07', NextWeekday::after($this->now(), 6, true)->format('Y-m-d'));
    }

    public function testSundayIsSeven(): void
    {
        self::assertSame('2026-11-08', NextWeekday::after($this->now(), 7)->format('Y-m-d'));
    }

    public function testResultIsMidnightInTheTimezoneOfTheReference(): void
    {
        self::assertSame('2026-11-06 00:00:00 Europe/Luxembourg', NextWeekday::after($this->now(), 5)->format('Y-m-d H:i:s e'));
    }

    public function testResultStaysMidnightAcrossFallBackTransition(): void
    {
        $now = new \DateTimeImmutable('2026-10-22 10:00:00', new \DateTimeZone('Europe/Luxembourg'));

        self::assertSame('2026-10-26 00:00:00', NextWeekday::after($now, 1)->format('Y-m-d H:i:s'));
    }

    public function testReferenceIsNotModified(): void
    {
        $now = $this->now();
        NextWeekday::after($now, 5);

        self::assertSame('2026-11-04 15:30:00', $now->format('Y-m-d H:i:s'));
    }

    public function testWeekdayZeroIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        NextWeekday::after($this->now(), 0);
    }

    public function testWeekdayEightIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        NextWeekday::after($this->now(), 8);
    }

    private function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-04 15:30:00', new \DateTimeZone('Europe/Luxembourg'));
    }
}
