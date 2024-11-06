<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Date;

use LibertJeremy\DateHelpers\Date\Formatter as LibertJeremyDateFormatter;

class Formatter
{
    public static function firstDayOfMonthByMonthNumber(int $month, ?int $year = null): \DateTimeInterface
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Month number must be between 1 and 12.');
        }

        $year = $year ?? (new \DateTime())->format('Y');

        return self::fromDayAndMonthAndYear(1, $month, $year);
    }

    public static function fromDayAndMonthAndYear(int $day, int $month, int $year): \DateTimeInterface
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Month number must be between 1 and 12.');
        }

        if (!checkdate($month, $day, $year)) {
            throw new \RuntimeException('Wrong date provided.');
        }

        return new \DateTime($year.'-'.str_pad((string)$month, 2, '0', \STR_PAD_LEFT).'-'.str_pad((string)$day, 2, '0', \STR_PAD_LEFT));
    }

    public static function fromDayAndMonthAndYearOrLastDayOfMonthIfNotExists(int $day, int $month, int $year): \DateTimeInterface
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Month number must be between 1 and 12.');
        }

        if (checkdate($month, $day, $year)) {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        return self::lastDayOfMonthByDate(\DateTime::createFromFormat('Y-m-d', $year.'-'.$month.'-01'), true);
    }

    public static function fromWeekNumber(int $week, int $year): \DateTimeInterface
    {
        return (new \DateTime())->setISODate($year, $week);
    }

    public static function firstDayOfWeekByDate(\DateTimeInterface $date): \DateTimeInterface
    {
        return (clone $date)->sub(new \DateInterval('P'.($date->format('N') - 1).'D'))->setTime(0, 0, 0);
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

    public static function lastDayOfMonthByMonthNumber(int $month, int $year, bool $withHours = true): \DateTimeInterface
    {
        return LibertJeremyDateFormatter::lastDayOfMonthByDate(self::firstDayOfMonthByMonthNumber($month, $year), $withHours);
    }

    public static function firstDayOfYearByYearNumber(int $year): \DateTimeInterface
    {
        return new \DateTime($year.'-01-01');
    }

    public static function firstDayOfYearByDate(\DateTimeInterface $date): \DateTimeInterface
    {
        return self::firstDayOfYearByYearNumber((int)$date->format('Y'));
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
