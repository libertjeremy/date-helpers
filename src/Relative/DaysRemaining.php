<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Relative;

final class DaysRemaining
{
    /**
     * Nombre de jours calendaires entre $now et $date : 0 le même jour, positif si $date est dans le futur, négatif dans le passé.
     *
     * Les deux dates sont lues dans le fuseau de $now et seul le jour du calendrier compte (pas les heures) :
     * un changement d'heure ou un fuseau différent ne décale donc jamais le résultat.
     */
    public static function between(\DateTimeImmutable $date, \DateTimeImmutable $now): int
    {
        $utc = new \DateTimeZone('UTC');
        $day = new \DateTimeImmutable($date->setTimezone($now->getTimezone())->format('Y-m-d'), $utc);
        $today = new \DateTimeImmutable($now->format('Y-m-d'), $utc);

        return (int) $today->diff($day)->format('%r%a');
    }
}
