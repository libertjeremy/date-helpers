<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Period;

use LibertJeremy\DateHelpers\Date\Shifter;

class Manipulator
{
    /**
     * @param \DatePeriod<\DateTimeInterface, \DateTimeInterface, null> $truncatedDatePeriod
     * @param \DatePeriod<\DateTimeInterface, \DateTimeInterface, null> $referenceDatePeriod
     *
     * @return \DatePeriod<\DateTimeInterface, \DateTimeInterface, null>|null
     */
    public static function truncate(\DatePeriod $truncatedDatePeriod, \DatePeriod $referenceDatePeriod): ?\DatePeriod
    {
        if (($truncatedEnd = $truncatedDatePeriod->getEndDate()) < ($referenceStart = $referenceDatePeriod->getStartDate())) {
            return null;
        }

        if (($truncatedStart = $truncatedDatePeriod->getStartDate()) > ($referenceEnd = $referenceDatePeriod->getEndDate())) {
            return null;
        }

        return Formatter::fromDates(
            max($truncatedStart, $referenceStart),
            min($truncatedEnd, $referenceEnd)
        );
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
     * @param array<\DatePeriod<\DateTimeInterface, \DateTimeInterface, null>> $periods
     *
     * @return list<\DatePeriod<\DateTimeInterface, \DateTimeInterface, null>>
     */
    public static function splitNonOverlappingPeriods(array $periods): array
    {
        if (empty($periods)) {
            return [];
        }

        // Collecter tous les points de rupture (dates importantes)
        $breakpoints = [];

        foreach ($periods as $period) {
            $breakpoints[] = clone $period->getStartDate();

            // Ajouter le jour suivant la fin comme point de rupture
            $breakpoints[] = Shifter::add($period->getEndDate(), new \DateInterval('PT1S'));
        }

        // Trier les points de rupture
        usort($breakpoints, static function ($a, $b) {
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
        for ($i = 0; $i < \count($uniqueBreakpoints) - 1; ++$i) {
            $start = clone $uniqueBreakpoints[$i];
            $end = Shifter::sub($uniqueBreakpoints[$i + 1], new \DateInterval('PT1S'));

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
