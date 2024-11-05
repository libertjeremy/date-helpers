<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Date;

class Parser
{
    public static function isWeekEnd(\DateTimeInterface $date): bool
    {
        return (int) $date->format('N') >= 6;
    }

    public static function isFirstDayOfMonthByDate(\DateTimeInterface $date): bool
    {
        return $date->format('Ymd') === Formatter::firstDayOfMonthByDate($date)->format('Ymd');
    }

    public static function isLastDayOfMonthByDate(\DateTimeInterface $date): bool
    {
        return $date->format('Ymd') === Formatter::lastDayOfMonthByDate($date)->format('Ymd');
    }

    /**
     * @param bool $strict Check hours if true
     */
    public static function isDayBefore(\DateTimeInterface $dateBefore, \DateTimeInterface $dateToCompare, bool $strict = false): bool
    {
        if (
            true === $strict
            && (
                '235959' !== $dateBefore->format('His')
                || '000000' !== $dateToCompare->format('His')
            )
        ) {
            return false;
        }

        return $dateBefore->format('Ymd') === (clone $dateToCompare)->sub(new \DateInterval('P1D'))->format('Ymd');
    }

    /**
     * @param bool $strict Check hours if true
     */
    public static function isDayAfter(\DateTimeInterface $dateAfter, \DateTimeInterface $dateToCompare, bool $strict = false): bool
    {
        if (true === $strict && ('000000' !== $dateAfter->format('His') || '235959' !== $dateToCompare->format('His'))) {
            return false;
        }

        return $dateAfter->format('Ymd') === (clone $dateToCompare)->add(new \DateInterval('P1D'))->format('Ymd');
    }

    public static function isBefore(\DateTimeInterface $dateBefore, \DateTimeInterface $dateToCompare): bool
    {
        return $dateBefore->format('YmdHis') < $dateToCompare->format('YmdHis');
    }

    public static function isAfter(\DateTimeInterface $dateAfter, \DateTimeInterface $dateToCompare): bool
    {
        return $dateAfter->format('YmdHis') > $dateToCompare->format('YmdHis');
    }
}
