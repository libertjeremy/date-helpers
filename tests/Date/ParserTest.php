<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Date;

use LibertJeremy\DateHelpers\Date\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testIsWeekEnd1(): void
    {
        self::assertFalse(Parser::isWeekEnd(new \DateTime('2022-10-25 00:00:00')));
    }

    public function testIsWeekEnd2(): void
    {
        self::assertFalse(Parser::isWeekEnd(new \DateTime('2022-10-31 15:30:35')));
    }

    public function testIsWeekEnd3(): void
    {
        self::assertTrue(Parser::isWeekEnd(new \DateTime('2022-10-29 00:00:00')));
    }

    public function testIsWeekEnd4(): void
    {
        self::assertTrue(Parser::isWeekEnd(new \DateTime('2022-10-30 15:15:15')));
    }

    public function testIsDayBefore1(): void
    {
        self::assertTrue(Parser::isDayBefore(new \DateTime('1993-08-12'), new \DateTime('1993-08-13')));
    }

    public function testIsDayBefore2(): void
    {
        self::assertFalse(Parser::isDayBefore(new \DateTime('1993-08-12'), new \DateTime('1993-08-11')));
    }

    public function testIsDayBefore3(): void
    {
        self::assertFalse(Parser::isDayBefore(new \DateTime('1993-08-12'), new \DateTime('1993-08-12')));
    }

    public function testIsDayBefore4(): void
    {
        self::assertTrue(Parser::isDayBefore(new \DateTime('1993-08-31'), new \DateTime('1993-09-01')));
    }

    public function testIsDayBefore5(): void
    {
        self::assertFalse(Parser::isDayBefore(new \DateTime('1993-08-31'), new \DateTime('1993-09-01'), true));
    }

    public function testIsDayBefore6(): void
    {
        self::assertTrue(Parser::isDayBefore(new \DateTime('1993-08-31 23:59:59'), new \DateTime('1993-09-01 00:00:00'), true));
    }

    public function testIsDayBefore7(): void
    {
        self::assertFalse(Parser::isDayBefore(new \DateTime('1993-08-31 23:59:58'), new \DateTime('1993-09-01 00:00:00'), true));
    }

    public function testIsDayBefore8(): void
    {
        self::assertFalse(Parser::isDayBefore(new \DateTime('1993-08-31 23:59:59'), new \DateTime('1993-09-02 00:00:00'), true));
    }

    public function testIsDayBefore9(): void
    {
        self::assertTrue(Parser::isDayBefore(new \DateTime('2021-11-14'), new \DateTime('2021-11-15'), false));
    }

    public function testIsDayAfter1(): void
    {
        self::assertTrue(Parser::isDayAfter(new \DateTime('1993-08-14'), new \DateTime('1993-08-13')));
    }

    public function testIsDayAfter2(): void
    {
        self::assertFalse(Parser::isDayAfter(new \DateTime('1993-08-12'), new \DateTime('1993-08-13')));
    }

    public function testIsDayAfter3(): void
    {
        self::assertFalse(Parser::isDayAfter(new \DateTime('1993-08-12'), new \DateTime('1993-08-12')));
    }

    public function testIsDayAfter4(): void
    {
        self::assertTrue(Parser::isDayAfter(new \DateTime('1993-09-01'), new \DateTime('1993-08-31')));
    }

    public function testIsDayAfter5(): void
    {
        self::assertTrue(Parser::isDayAfter(new \DateTime('1993-09-01 00:00:00'), new \DateTime('1993-08-31 23:59:59'), true));
    }

    public function testIsDayAfter6(): void
    {
        self::assertFalse(Parser::isDayAfter(new \DateTime('1993-09-01 00:00:00'), new \DateTime('1993-08-31 23:59:58'), true));
    }
}
