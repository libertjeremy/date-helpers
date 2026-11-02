<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Parser;

use LibertJeremy\DateHelpers\Relative\NextWeekday;

/**
 * Reconnaît une date relative dans une phrase française (insensible à la casse et aux accents).
 *
 * Expressions reconnues (le premier fragment trouvé dans le texte l'emporte, à égalité le plus long) :
 *
 * - "aujourd'hui", "ce matin", "ce midi", "cet après-midi", "ce soir", "cette nuit" : le jour de référence
 * - "demain", "après-demain", éventuellement suivis de "matin", "midi", "après-midi", "soir", "nuit"
 * - un nom de jour : "lundi" et "lundi prochain" désignent la prochaine occurrence STRICTEMENT après aujourd'hui,
 *   "ce lundi" la prochaine occurrence aujourd'hui compris (éventuellement suivi d'un moment : "lundi soir")
 * - "dans N jours / semaines / mois", N en chiffres ou en lettres (voir {@see FrenchNumberParser}), entier et positif
 * - "la semaine prochaine" : le lundi suivant (strictement après aujourd'hui)
 * - "fin de semaine" : le samedi à venir (aujourd'hui compris)
 * - "le 12" : prochain 12 du mois, aujourd'hui compris
 * - "le 12 mars", "12 mars 2027", "12/03", "12/03/2027" : prochaine occurrence aujourd'hui comprise, ou date exacte si l'année est donnée
 *
 * Confiance : 1.0 pour une date exacte ou une expression non ambiguë (aujourd'hui, demain, dans N jours, date complète),
 * 0.9 pour un jour de semaine ou un jour et un mois sans année, 0.7 pour "le 12" seul, 0.6 pour
 * "la semaine prochaine" et "fin de semaine".
 *
 * Toutes les dates retournées sont à minuit dans le fuseau de $now ; l'horloge système n'est jamais lue.
 *
 * @phpstan-type Candidate array{start: int, end: int, date: \DateTimeImmutable, period: ?TimeOfDay, confidence: float}
 */
final class FrenchRelativeDateParser
{
    private const WEEKDAYS = ['lundi' => 1, 'mardi' => 2, 'mercredi' => 3, 'jeudi' => 4, 'vendredi' => 5, 'samedi' => 6, 'dimanche' => 7];

    private const MONTHS = [
        'janvier' => 1, 'fevrier' => 2, 'mars' => 3, 'avril' => 4, 'mai' => 5, 'juin' => 6,
        'juillet' => 7, 'aout' => 8, 'septembre' => 9, 'octobre' => 10, 'novembre' => 11, 'decembre' => 12,
    ];

    private const PERIOD = 'matin|midi|apres[ -]?midi|soir|nuit';

    public function parse(string $text, \DateTimeImmutable $now): ?ParsedRelativeDate
    {
        [$normalized, $offsets] = FrenchText::normalizeWithOffsets($text);
        $today = $now->setTime(0, 0);

        $candidates = [
            ...$this->today($normalized, $today),
            ...$this->tomorrow($normalized, $today),
            ...$this->weekdays($normalized, $today),
            ...$this->inAmount($normalized, $today),
            ...$this->nextWeek($normalized, $today),
            ...$this->endOfWeek($normalized, $today),
            ...$this->numericDates($normalized, $today),
            ...$this->namedMonthDates($normalized, $today),
            ...$this->dayOfMonth($normalized, $today),
        ];

        $best = null;
        foreach ($candidates as $candidate) {
            if (null === $best
                || $candidate['start'] < $best['start']
                || ($candidate['start'] === $best['start'] && $candidate['end'] > $best['end'])) {
                $best = $candidate;
            }
        }
        if (null === $best) {
            return null;
        }

        $from = $offsets[$best['start']];
        $to = $offsets[$best['end']];

        return new ParsedRelativeDate($best['date'], $best['period'], substr($text, $from, $to - $from), $best['confidence'], $from);
    }

    /**
     * @return list<Candidate>
     */
    private function today(string $text, \DateTimeImmutable $today): array
    {
        $candidates = [];
        foreach ($this->matches('/\baujourd\'?hui\b/', $text) as $match) {
            $candidates[] = $this->candidate($match, $today, null, 1.0);
        }
        foreach ($this->matches('/\b(?:ce (?:matin|midi|soir)|cet apres[ -]?midi|cette nuit)\b/', $text) as $match) {
            $candidates[] = $this->candidate($match, $today, TimeOfDay::fromFrenchWord($match[0][0]), 1.0);
        }

        return $candidates;
    }

    /**
     * @return list<Candidate>
     */
    private function tomorrow(string $text, \DateTimeImmutable $today): array
    {
        $candidates = [];
        foreach ($this->matches('/\bdemain(?:\s+('.self::PERIOD.'))?\b/', $text) as $match) {
            $candidates[] = $this->candidate($match, $today->modify('+1 day'), TimeOfDay::fromFrenchWord($match[1][0] ?? ''), 1.0);
        }
        foreach ($this->matches('/\bapres[ -]?demain(?:\s+('.self::PERIOD.'))?\b/', $text) as $match) {
            $candidates[] = $this->candidate($match, $today->modify('+2 days'), TimeOfDay::fromFrenchWord($match[1][0] ?? ''), 1.0);
        }

        return $candidates;
    }

    /**
     * @return list<Candidate>
     */
    private function weekdays(string $text, \DateTimeImmutable $today): array
    {
        $names = implode('|', array_keys(self::WEEKDAYS));
        $candidates = [];
        foreach ($this->matches('/\b(ce\s+)?('.$names.')(\s+prochain)?(?:\s+('.self::PERIOD.'))?\b/', $text) as $match) {
            $includeToday = '' !== ($match[1][0] ?? '') && '' === ($match[3][0] ?? '');
            $date = NextWeekday::after($today, self::WEEKDAYS[$match[2][0]], $includeToday);
            $candidates[] = $this->candidate($match, $date, TimeOfDay::fromFrenchWord($match[4][0] ?? ''), 0.9);
        }

        return $candidates;
    }

    /**
     * @return list<Candidate>
     */
    private function inAmount(string $text, \DateTimeImmutable $today): array
    {
        $pattern = '/\bdans\s+((?:(?!dans\b)[a-z0-9,.]+)(?:[ -](?!dans\b)[a-z]+){0,3}?)\s+(jours?|semaines?|mois)\b/';
        $candidates = [];
        foreach ($this->matches($pattern, $text) as $match) {
            $amount = FrenchNumberParser::parse($match[1][0]);
            if (null === $amount || $amount < 1 || floor($amount) !== $amount) {
                continue;
            }
            $count = (int) $amount;
            $date = match ($match[2][0][0]) {
                'j' => $today->modify(\sprintf('+%d days', $count)),
                's' => $today->modify(\sprintf('+%d days', $count * 7)),
                default => $this->addMonths($today, $count),
            };
            $candidates[] = $this->candidate($match, $date, null, 1.0);
        }

        return $candidates;
    }

    /**
     * @return list<Candidate>
     */
    private function nextWeek(string $text, \DateTimeImmutable $today): array
    {
        $candidates = [];
        foreach ($this->matches('/\b(?:la\s+)?semaine\s+prochaine\b/', $text) as $match) {
            $candidates[] = $this->candidate($match, NextWeekday::after($today, 1), null, 0.6);
        }

        return $candidates;
    }

    /**
     * @return list<Candidate>
     */
    private function endOfWeek(string $text, \DateTimeImmutable $today): array
    {
        $candidates = [];
        foreach ($this->matches('/\bfin\s+de\s+(?:la\s+)?semaine\b/', $text) as $match) {
            $candidates[] = $this->candidate($match, NextWeekday::after($today, 6, true), null, 0.6);
        }

        return $candidates;
    }

    /**
     * @return list<Candidate>
     */
    private function numericDates(string $text, \DateTimeImmutable $today): array
    {
        $candidates = [];
        foreach ($this->matches('/(?:\ble\s+)?(?<![\d\/])(\d{1,2})\/(\d{1,2})(?:\/(\d{4}|\d{2}))?(?![\d\/])/', $text) as $match) {
            $year = isset($match[3]) && '' !== $match[3][0] ? (int) $match[3][0] : null;
            $year = null !== $year && $year < 100 ? 2000 + $year : $year;
            $date = $this->resolveDate($today, (int) $match[1][0], (int) $match[2][0], $year);
            if (null !== $date) {
                $candidates[] = $this->candidate($match, $date, null, null === $year ? 0.9 : 1.0);
            }
        }

        return $candidates;
    }

    /**
     * @return list<Candidate>
     */
    private function namedMonthDates(string $text, \DateTimeImmutable $today): array
    {
        $months = implode('|', array_keys(self::MONTHS));
        $candidates = [];
        foreach ($this->matches('/(?:\ble\s+)?\b(1er|\d{1,2})\s+('.$months.')\b(?:\s+(\d{4})\b)?/', $text) as $match) {
            $year = isset($match[3]) && '' !== $match[3][0] ? (int) $match[3][0] : null;
            $date = $this->resolveDate($today, (int) $match[1][0], self::MONTHS[$match[2][0]], $year);
            if (null !== $date) {
                $candidates[] = $this->candidate($match, $date, null, null === $year ? 0.9 : 1.0);
            }
        }

        return $candidates;
    }

    /**
     * @return list<Candidate>
     */
    private function dayOfMonth(string $text, \DateTimeImmutable $today): array
    {
        $candidates = [];
        foreach ($this->matches('/\ble\s+(1er|\d{1,2})\b(?![\/:])(?!\s*(?:euros?|%|€|\$))/', $text) as $match) {
            $day = (int) $match[1][0];
            for ($ahead = 0; $ahead <= 13; ++$ahead) {
                $month = $this->addMonths($today->setDate((int) $today->format('Y'), (int) $today->format('n'), 1), $ahead);
                $date = $this->resolveDate($month, $day, (int) $month->format('n'), (int) $month->format('Y'));
                if (null !== $date && $date >= $today) {
                    $candidates[] = $this->candidate($match, $date, null, 0.7);
                    break;
                }
            }
        }

        return $candidates;
    }

    /**
     * Jour et mois donnés : la date exacte si l'année est connue, sinon la prochaine occurrence (aujourd'hui compris).
     */
    private function resolveDate(\DateTimeImmutable $today, int $day, int $month, ?int $year): ?\DateTimeImmutable
    {
        $currentYear = (int) $today->format('Y');
        $years = null === $year ? range($currentYear, $currentYear + 8) : [$year];
        foreach ($years as $candidateYear) {
            if (!checkdate($month, $day, $candidateYear)) {
                continue;
            }
            $date = $today->setDate($candidateYear, $month, $day);
            if (null !== $year || $date >= $today) {
                return $date;
            }
        }

        return null;
    }

    /**
     * Ajoute des mois en ramenant au dernier jour du mois d'arrivée si besoin (31 janvier + 1 mois = 28 ou 29 février).
     */
    private function addMonths(\DateTimeImmutable $date, int $months): \DateTimeImmutable
    {
        $index = (int) $date->format('Y') * 12 + (int) $date->format('n') - 1 + $months;
        $year = intdiv($index, 12);
        $month = $index % 12 + 1;
        $lastDay = (int) $date->setDate($year, $month, 1)->format('t');

        return $date->setDate($year, $month, min((int) $date->format('j'), $lastDay));
    }

    /**
     * @param array<array{string, int}> $match
     *
     * @return Candidate
     */
    private function candidate(array $match, \DateTimeImmutable $date, ?TimeOfDay $period, float $confidence): array
    {
        return ['start' => $match[0][1], 'end' => $match[0][1] + \strlen($match[0][0]), 'date' => $date, 'period' => $period, 'confidence' => $confidence];
    }

    /**
     * @return list<array<array{string, int}>>
     */
    private function matches(string $pattern, string $text): array
    {
        if (false === preg_match_all($pattern, $text, $found, \PREG_SET_ORDER | \PREG_OFFSET_CAPTURE)) {
            return [];
        }

        return $found;
    }
}
