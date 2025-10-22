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

    /**
     * Divise une période en sous-périodes non chevauchantes en excluant des plages de dates spécifiques.
     *
     * Prend une période principale et une liste de périodes à exclure, puis retourne les plages
     * de dates "libres" qui ne sont pas couvertes par les exclusions.
     *
     * Utile pour : gestion de disponibilités, planification, calcul de jours travaillés.
     *
     * Exemple :
     * - Période : 1-31 janvier
     * - Exclusions : 5-10 janvier, 20-25 janvier
     * - Résultat : [1-4 janvier, 11-19 janvier, 26-31 janvier]
     *
     * Les périodes à exclure qui se chevauchent sont fusionnées automatiquement.
     *
     * @param array<\DatePeriod> $periods
     *
     * @return array<\DatePeriod>
     *
     * @return array Tableau de périodes libres [['start' => DateTime, 'end' => DateTime], ...]
     */
    public static function splitNonOverlappingPeriods(array $periods): array
    {
        if (empty($periods)) {
            return [];
        }

        // Collecter tous les points de rupture (dates importantes)
        $breakpoints = [];

        foreach ($periods as $period) {
            $start = clone $period->getStartDate();
            $end = clone $period->getEndDate();

            $breakpoints[] = $start;

            // Ajouter le jour suivant la fin comme point de rupture
            $nextDay = clone $end;
            $nextDay->modify('+1 second');
            $breakpoints[] = $nextDay;
        }

        // Trier les points de rupture
        usort($breakpoints, function($a, $b) {
            return $a <=> $b;
        });

        // Supprimer les doublons
        $uniqueBreakpoints = [];
        $lastTimestamp = null;
        foreach ($breakpoints as $bp) {
            $timestamp = $bp->getTimestamp();
            if ($timestamp !== $lastTimestamp) {
                $uniqueBreakpoints[] = clone $bp;
                $lastTimestamp = $timestamp;
            }
        }

        // Créer les périodes candidates entre chaque paire de points
        $candidatePeriods = [];
        for ($i = 0; $i < count($uniqueBreakpoints) - 1; $i++) {
            $start = clone $uniqueBreakpoints[$i];
            $end = clone $uniqueBreakpoints[$i + 1];
            $end->modify('-1 second');

            // Vérifier si cette période est couverte par au moins une période originale
            $isCovered = false;
            foreach ($periods as $original) {
                $origStart = $original->getStartDate();
                $origEnd = $original->getEndDate();

                if ($start >= $origStart && $end <= $origEnd) {
                    $isCovered = true;
                    break;
                }
            }

            if ($isCovered) {
                $candidatePeriods[] = Formatter::fromDates(
                    $start,
                    $end
                );
            }
        }

        return $candidatePeriods;
    }
}
