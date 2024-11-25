<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Period;

use LibertJeremy\DateHelpers\Period\Formatter;
use LibertJeremy\DateHelpers\Period\Manipulator;
use PHPUnit\Framework\TestCase;

class ManipulatorTest extends TestCase
{
    public function testTruncate1(): void
    {
        $truncatedDatePeriod = Formatter::fromDates(new \DateTime('2021-11-30'), new \DateTime('2022-01-30'));
        $referenceDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));
        $expectedDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));

        self::assertEquals($expectedDatePeriod, Manipulator::truncate($truncatedDatePeriod, $referenceDatePeriod));
    }

    public function testTruncate2(): void
    {
        $truncatedDatePeriod = Formatter::fromDates(new \DateTime('2021-11-30'), new \DateTime('2021-12-12'));
        $referenceDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));
        $expectedDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-12'));

        self::assertEquals($expectedDatePeriod, Manipulator::truncate($truncatedDatePeriod, $referenceDatePeriod));
    }

    public function testTruncate3(): void
    {
        $truncatedDatePeriod = Formatter::fromDates(new \DateTime('2021-12-08'), new \DateTime('2021-12-12'));
        $referenceDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));
        $expectedDatePeriod = Formatter::fromDates(new \DateTime('2021-12-08'), new \DateTime('2021-12-12'));

        self::assertEquals($expectedDatePeriod, Manipulator::truncate($truncatedDatePeriod, $referenceDatePeriod));
    }

    public function testTruncate4(): void
    {
        $truncatedDatePeriod = Formatter::fromDates(new \DateTime('2020-12-08'), new \DateTime('2020-12-12'));
        $referenceDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));

        self::assertNull(Manipulator::truncate($truncatedDatePeriod, $referenceDatePeriod));
    }
}
