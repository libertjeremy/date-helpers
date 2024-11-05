<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Date;

class Parser
{
    public static function isWeekEnd(\DateTimeInterface $date): bool
    {
        return (int) $date->format('N') >= 6;
    }

    public static function isLastDayOfMonthByDate(\DateTimeInterface $date): bool
    {
        return $date->format('Ymd') === Formatter::lastDayOfMonthByDate($date)->format('Ymd');
    }
}
