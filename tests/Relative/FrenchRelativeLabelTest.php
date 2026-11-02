<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Relative;

use LibertJeremy\DateHelpers\Relative\FrenchRelativeLabel;
use PHPUnit\Framework\TestCase;

/**
 * Jour de référence : mercredi 4 novembre 2026, 15 h (Europe/Luxembourg).
 */
class FrenchRelativeLabelTest extends TestCase
{
    public function testTodayIsAujourdhui(): void
    {
        self::assertSame('Aujourd\'hui', FrenchRelativeLabel::forDate($this->lux('2026-11-04 22:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testTomorrowIsDemain(): void
    {
        self::assertSame('Demain', FrenchRelativeLabel::forDate($this->lux('2026-11-05 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testYesterdayIsHier(): void
    {
        self::assertSame('Hier', FrenchRelativeLabel::forDate($this->lux('2026-11-03 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testTwoDaysAreCountdown(): void
    {
        self::assertSame('J-2', FrenchRelativeLabel::forDate($this->lux('2026-11-06 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testThirteenDaysAreStillCountdown(): void
    {
        self::assertSame('J-13', FrenchRelativeLabel::forDate($this->lux('2026-11-17 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testFourteenDaysAreTwoWeeks(): void
    {
        self::assertSame('Dans 2 sem.', FrenchRelativeLabel::forDate($this->lux('2026-11-18 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testTwentyDaysRoundDownToTwoWeeks(): void
    {
        self::assertSame('Dans 2 sem.', FrenchRelativeLabel::forDate($this->lux('2026-11-24 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testFiftyNineDaysAreEightWeeks(): void
    {
        self::assertSame('Dans 8 sem.', FrenchRelativeLabel::forDate($this->lux('2027-01-02 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testSixtyDaysAreTwoMonths(): void
    {
        self::assertSame('Dans 2 mois', FrenchRelativeLabel::forDate($this->lux('2027-01-03 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testNinetyFiveDaysRoundDownToThreeMonths(): void
    {
        self::assertSame('Dans 3 mois', FrenchRelativeLabel::forDate($this->lux('2027-02-07 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testTwoDaysAgo(): void
    {
        self::assertSame('Il y a 2 j', FrenchRelativeLabel::forDate($this->lux('2026-11-02 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testFortyDaysAgo(): void
    {
        self::assertSame('Il y a 40 j', FrenchRelativeLabel::forDate($this->lux('2026-09-25 08:00'), $this->lux('2026-11-04 15:00')));
    }

    public function testLabelIgnoresTheHoursOfTheDeadline(): void
    {
        self::assertSame('Demain', FrenchRelativeLabel::forDate($this->lux('2026-11-05 00:00'), $this->lux('2026-11-04 23:59')));
    }

    private function lux(string $dateTime): \DateTimeImmutable
    {
        return new \DateTimeImmutable($dateTime, new \DateTimeZone('Europe/Luxembourg'));
    }
}
