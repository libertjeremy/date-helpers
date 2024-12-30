<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Date;

class Comparator
{
    public static function isYearLess(\DateTimeInterface $date, \DateTimeInterface $dateToCheck): bool
    {
        return (clone $date)->format('Y') < (clone $dateToCheck)->format('Y');
    }

    public static function isCurrentYear(\DateTimeInterface $date, \DateTimeInterface $dateToCheck): bool
    {
        return (clone $date)->format('Y') === (clone $dateToCheck)->format('Y');
    }

    public static function isYearGreater(\DateTimeInterface $date, \DateTimeInterface $dateToCheck): bool
    {
        return (clone $date)->format('Y') > (clone $dateToCheck)->format('Y');
    }
}
