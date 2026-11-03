<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Relative;

final class NextWeekday
{
    /**
     * Prochain jour de semaine ISO (1 = lundi ... 7 = dimanche), à minuit dans le fuseau de $now.
     *
     * Strictement après $now, sauf si $includeToday est vrai et que $now tombe déjà ce jour-là.
     *
     * @throws \InvalidArgumentException si $isoWeekday n'est pas compris entre 1 et 7
     */
    public static function after(\DateTimeImmutable $now, int $isoWeekday, bool $includeToday = false): \DateTimeImmutable
    {
        if ($isoWeekday < 1 || $isoWeekday > 7) {
            throw new \InvalidArgumentException(\sprintf('ISO weekday must be between 1 and 7, %d given.', $isoWeekday));
        }

        $today = $now->setTime(0, 0);
        $distance = ($isoWeekday - (int) $today->format('N') + 7) % 7;
        if (0 === $distance && !$includeToday) {
            $distance = 7;
        }

        return $today->modify(\sprintf('+%d days', $distance));
    }
}
