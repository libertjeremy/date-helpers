<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Period;

use LibertJeremy\DateHelpers\Date\Formatter as DateFormatter;

class Formatter
{
    protected const DEFAULT_INTERVAL = 'P1D';

    public static function fromDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate, bool $allDay = false): \DatePeriod
    {
        $tmpStartDate = clone $startDate;
        $tmpEndDate = clone $endDate;

        if (true === $allDay) {
            $tmpStartDate = DateFormatter::toStartOfDay($tmpStartDate);
            $tmpEndDate = DateFormatter::toEndOfDay($tmpEndDate);
        }

        return new \DatePeriod($tmpStartDate, new \DateInterval(self::DEFAULT_INTERVAL), $tmpEndDate);
    }

    public static function monthByMonthNumber(int $month, int $year, bool $allDay = true): \DatePeriod
    {
        return self::fromDates(
            DateFormatter::firstDayOfMonthByMonthNumber($month, $year),
            DateFormatter::lastDayOfMonthByMonthNumber($month, $year),
            $allDay
        );
    }

    public static function yearByYearNumber(int $year): \DatePeriod
    {
        return self::fromDates(
            DateFormatter::firstDayOfYearByYearNumber($year),
            DateFormatter::lastDayOfYearByYearNumber($year),
            true
        );
    }
}
