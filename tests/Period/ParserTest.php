<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Period;

use LibertJeremy\DateHelpers\Period\Formatter;
use LibertJeremy\DateHelpers\Period\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testIsGreater1(): void
    {
        self::assertFalse(
            Parser::isGreater(
                Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2023-03-31'), true),
                Formatter::fromDates(new \DateTime('2022-11-01'), new \DateTime('2023-03-31'), true)
            )
        );
    }

    public function testIsGreater2(): void
    {
        self::assertFalse(
            Parser::isGreater(
                Formatter::fromDates(new \DateTime('2022-11-01'), new \DateTime('2023-03-31'), true),
                Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2023-03-31'), true)
            )
        );
    }

    public function testIsGreater3(): void
    {
        self::assertTrue(
            Parser::isGreater(
                Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2023-03-31'), true),
                Formatter::fromDates(new \DateTime('2022-11-01'), new \DateTime('2023-03-31'), true),
                false
            )
        );
    }

    public function testIsGreater4(): void
    {
        self::assertFalse(
            Parser::isGreater(
                Formatter::fromDates(new \DateTime('2022-11-01'), new \DateTime('2023-03-31'), true),
                Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2023-03-31'), true),
                false
            )
        );
    }

    public function testContains1(): void
    {
        self::assertTrue(
            Parser::contains(
                Formatter::fromDates(new \DateTime('2022-11-01'), new \DateTime('2023-03-31'), true),
                new \DateTime('2023-01-01')
            )
        );
    }

    public function testContains2(): void
    {
        self::assertFalse(
            Parser::contains(
                Formatter::fromDates(new \DateTime('2022-11-01'), new \DateTime('2023-03-31'), true),
                new \DateTime('2024-01-01')
            )
        );
    }

    public function testIsAllMonth1(): void
    {
        self::assertTrue(Parser::isAllMonth(Formatter::monthByMonthNumber(1, 2023)));
    }

    public function testIsAllMonth2(): void
    {
        self::assertFalse(Parser::isAllMonth(Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2023-02-27'))));
    }

    public function testIsAllMonth3(): void
    {
        self::assertTrue(Parser::isAllMonth(Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2023-02-28'))));
    }

    public function testGetTotalMonthsFromDatePeriod1(): void
    {
        self::assertSame(6, Parser::retrieveTotalMonths(Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2023-06-30'))));
    }

    public function testGetTotalMonthsFromDatePeriod2(): void
    {
        self::assertSame(13, Parser::retrieveTotalMonths(Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2024-02-25'))));
    }

    public function testGetTotalMonthsFromDatePeriod3(): void
    {
        self::assertSame(12, Parser::retrieveTotalMonths(Formatter::fromDates(new \DateTime('2023-01-01'), new \DateTime('2023-12-31'))));
    }
}
