<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Period;

use LibertJeremy\DateHelpers\Date\Formatter as DateFormatter;

class Parser
{
    public static function contains(\DatePeriod $datePeriod, \DateTimeInterface $date, bool $strict = true): bool
    {
        if (true === $strict) {
            return
                $date > $datePeriod->getStartDate()
                && $date < $datePeriod->getEndDate();
        }

        return
            $date >= $datePeriod->getStartDate()
            && $date <= $datePeriod->getEndDate();
    }

    public static function isGreater(\DatePeriod $checkedDatePeriod, \DatePeriod $baseDatePeriod, bool $strict = true): bool
    {
        if (true === $strict) {
            return
                $checkedDatePeriod->getStartDate() > $baseDatePeriod->getStartDate()
                && $checkedDatePeriod->getEndDate() < $baseDatePeriod->getEndDate();
        }

        return
            $checkedDatePeriod->getStartDate() >= $baseDatePeriod->getStartDate()
            && $checkedDatePeriod->getEndDate() <= $baseDatePeriod->getEndDate();
    }

    public static function isAllMonth(\DatePeriod $datePeriod, bool $strict = false): bool
    {
        if (1 !== (int) $datePeriod->getStartDate()->format('d')) {
            return false;
        }

        $datePeriodEndDate = $datePeriod->getEndDate();

        if (true === $strict) {
            return DateFormatter::lastDayOfMonthByDate($datePeriodEndDate, true)->format('YmdHis') === $datePeriodEndDate->format('YmdHis');
        }

        return DateFormatter::lastDayOfMonthByDate($datePeriodEndDate, false)->format('Ymd') === $datePeriodEndDate->format('Ymd');
    }
}
