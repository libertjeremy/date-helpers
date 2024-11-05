<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Date;

class Formatter
{
    public static function fromWeekNumber(int $week, int $year): \DateTimeInterface
    {
        return (new \DateTime())->setISODate($year, $week);
    }

    public static function lastDayOfWeekByDate(\DateTimeInterface $date): \DateTimeInterface
    {
        return (clone $date)->modify('+'.(7 - $date->format('N')).' days')->setTime(23, 59, 59);
    }

    public static function firstDayOfMonthByDate(\DateTimeInterface $date): \DateTimeInterface
    {
        return new \DateTime($date->format('Y-m').'-01');
    }
    
    public static function lastDayOfMonthByDate(\DateTimeInterface $date, bool $withHours = true): \DateTimeInterface
    {
        $date = new \DateTime($date->format('Y-m-t'));

        if (true === $withHours) {
            $date->setTime(23, 59, 59);
        }

        return $date;
    }

    public static function lastDayOfYearByYearNumber(int $year): \DateTimeInterface
    {
        return new \DateTime($year.'-12-31');
    }

    public static function lastDayOfYearByDate(\DateTimeInterface $date): \DateTimeInterface
    {
        return self::lastDayOfYearByYearNumber((int)$date->format('Y'));
    }
}
