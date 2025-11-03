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

    public static function weekByDate(\DateTimeInterface $date): \DatePeriod
    {
        return self::fromDates(
            DateFormatter::firstDayOfWeekByDate($date),
            DateFormatter::lastDayOfWeekByDate($date),
            true
        );
    }

    /**
     * @param array<\DatePeriod> $datePeriods
     *
     * @return array<\DatePeriod>
     */
    public static function splitPeriodsByMonth(array $datePeriods): array
    {
        if (empty($datePeriods)) {
            return [];
        }

        $earliestStart = null;
        $latestEnd = null;

        foreach ($datePeriods as $period) {
            $start = $period->getStartDate();
            $end = $period->getEndDate();

            if ($end === null) {
                continue;
            }

            if ($earliestStart === null || $start < $earliestStart) {
                $earliestStart = clone $start;
            }

            if ($latestEnd === null || $end > $latestEnd) {
                $latestEnd = clone $end;
            }
        }

        if ($earliestStart === null || $latestEnd === null) {
            return [];
        }

        $result = [];

        $firstOfMonth = (clone $earliestStart)->modify('first day of this month')->setTime(0, 0, 0);
        if ($earliestStart > $firstOfMonth) {
            $headEnd = (clone $earliestStart)->modify('-1 second');
            if ($headEnd >= $firstOfMonth) {
                $result[] = Formatter::fromDates($firstOfMonth, $headEnd);
            }
        }

        $current = clone $earliestStart;
        while ($current < $latestEnd) {
            $endOfMonth = (clone $current)->modify('last day of this month')->setTime(23, 59, 59);

            $periodEnd = ($endOfMonth < $latestEnd) ? $endOfMonth : $latestEnd;

            $inclusiveEnd = $periodEnd;

            $result[] = Formatter::fromDates($current, $inclusiveEnd);

            $current = (clone $current)->modify('first day of next month')->setTime(0, 0, 0);
        }

        $endOfLatestMonth = (clone $latestEnd)->modify('last day of this month')->setTime(23, 59, 59);
        if ($latestEnd < $endOfLatestMonth) {
            $tailStart = (clone $latestEnd)->modify('+1 second');
            $tailEnd = clone $endOfLatestMonth;
            $result[] = Formatter::fromDates($tailStart, $tailEnd);
        }

        return $result;
    }
}
