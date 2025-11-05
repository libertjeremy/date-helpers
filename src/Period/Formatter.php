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
        $result = [];

        foreach ($datePeriods as $period) {
            $start = $period->getStartDate();
            $end = $period->getEndDate();

            $currentStart = clone $start;

            while ($currentStart <= $end) {
                // Dernier jour du mois en cours
                $monthEnd = new \DateTime($currentStart->format('Y-m-t'));

                // Si la période se termine avant la fin du mois
                $currentEnd = ($end < $monthEnd) ? $end : $monthEnd;

                // Si c'est un mois complet (du 1er au dernier jour)
                if ($currentStart->format('d') === '01' && $currentEnd->format('d') === $currentStart->format('t')) {
                    $result[] = self::monthByMonthNumber(
                        (int) $currentStart->format('m'),
                        (int) $currentStart->format('Y')
                    );
                } else {
                    // Sinon, créer une période partielle
                    $result[] = self::fromDates(clone $currentStart, clone $currentEnd);
                }

                // Passer au premier jour du mois suivant
                $currentStart = (clone $monthEnd)->modify('+1 day');
            }
        }

        return $result;
    }
}
