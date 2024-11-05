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
}
