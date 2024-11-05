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
}
