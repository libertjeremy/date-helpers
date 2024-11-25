<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Period;

class Manipulator
{
    public static function truncate(\DatePeriod $truncatedDatePeriod, \DatePeriod $referenceDatePeriod): ?\DatePeriod
    {
        if (($truncatedDatePeriodEndDate = $truncatedDatePeriod->getEndDate()) < ($referenceDatePeriodStartDate = $referenceDatePeriod->getStartDate())) {
            return null;
        }

        if (($truncatedDatePeriodStartDate = $truncatedDatePeriod->getStartDate()) < $referenceDatePeriodStartDate) {
            $startDate = $referenceDatePeriodStartDate;
        } else {
            $startDate = $truncatedDatePeriodStartDate;
        }

        if ($truncatedDatePeriodEndDate > ($referenceDatePeriodEndDate = $referenceDatePeriod->getEndDate())) {
            $endDate = $referenceDatePeriodEndDate;
        } else {
            $endDate = $truncatedDatePeriodEndDate;
        }

        if ($endDate < $startDate) {
            return null;
        }

        return Formatter::fromDates($startDate, $endDate);
    }
}
