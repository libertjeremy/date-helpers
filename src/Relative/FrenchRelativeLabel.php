<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Relative;

final class FrenchRelativeLabel
{
    /**
     * Libellé français d'une échéance, lu comme un compte à rebours. N est le nombre de jours calendaires
     * jusqu'à $date (voir {@see DaysRemaining::between()}) :
     *
     * - N = 0 : "Aujourd'hui"
     * - N = 1 : "Demain"
     * - N de 2 à 13 : "J-N" (il reste N jours)
     * - N de 14 à 59 : "Dans N sem." (N / 7, arrondi à l'inférieur)
     * - N >= 60 : "Dans N mois" (N / 30, arrondi à l'inférieur)
     * - N = -1 : "Hier"
     * - N <= -2 : "Il y a N j" (N en valeur absolue)
     */
    public static function forDate(\DateTimeImmutable $date, \DateTimeImmutable $now): string
    {
        $days = DaysRemaining::between($date, $now);

        return match (true) {
            0 === $days => "Aujourd'hui",
            1 === $days => 'Demain',
            -1 === $days => 'Hier',
            $days < 0 => \sprintf('Il y a %d j', -$days),
            $days < 14 => \sprintf('J-%d', $days),
            $days < 60 => \sprintf('Dans %d sem.', intdiv($days, 7)),
            default => \sprintf('Dans %d mois', intdiv($days, 30)),
        };
    }
}
