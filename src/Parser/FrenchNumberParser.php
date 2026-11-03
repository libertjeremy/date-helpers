<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Parser;

/**
 * Lit les nombres et fractions du français parlé : "trois", "vingt-deux", "une douzaine", "les trois quarts", "1,5".
 *
 * Insensible à la casse et aux accents. Retourne null pour tout ce qui n'est pas reconnu.
 */
final class FrenchNumberParser
{
    private const UNITS = [
        'zero' => 0, 'un' => 1, 'une' => 1, 'deux' => 2, 'trois' => 3, 'quatre' => 4, 'cinq' => 5,
        'six' => 6, 'sept' => 7, 'huit' => 8, 'neuf' => 9, 'dix' => 10, 'onze' => 11, 'douze' => 12,
        'treize' => 13, 'quatorze' => 14, 'quinze' => 15, 'seize' => 16, 'vingt' => 20, 'trente' => 30,
        'quarante' => 40, 'cinquante' => 50, 'soixante' => 60, 'quatrevingt' => 80,
    ];

    private const FRACTIONS = [
        'une douzaine' => 12.0,
        'douzaine' => 12.0,
        'une demi douzaine' => 6.0,
        'demi douzaine' => 6.0,
        'une dizaine' => 10.0,
        'une quinzaine' => 15.0,
        'un quart' => 0.25,
        'quart' => 0.25,
        'trois quarts' => 0.75,
        'moitie' => 0.5,
        'un demi' => 0.5,
        'une demie' => 0.5,
        'demi' => 0.5,
        'demie' => 0.5,
        'un tiers' => 1 / 3,
        'tiers' => 1 / 3,
        'deux tiers' => 2 / 3,
    ];

    public static function parse(string $word): ?float
    {
        $text = FrenchText::normalize($word);
        $text = preg_replace('/[\s\-]+/', ' ', $text) ?? '';
        $text = trim($text);
        if ('' === $text) {
            return null;
        }

        if (1 === preg_match('/^\d+(?:[.,]\d+)?$/', $text)) {
            return (float) str_replace(',', '.', $text);
        }

        $text = preg_replace('/^(?:l\'|le |la |les )/', '', $text) ?? $text;

        return self::FRACTIONS[$text] ?? self::parseWords($text);
    }

    private static function parseWords(string $text): ?float
    {
        $text = str_replace(['quatre vingts', 'quatre vingt'], 'quatrevingt', $text);

        $total = 0;
        $previous = null;
        $hasNumber = false;
        foreach (explode(' ', $text) as $token) {
            if ('et' === $token) {
                continue;
            }
            if ('cent' === $token || 'cents' === $token) {
                $total = (0 === $total ? 1 : $total) * 100;
                $previous = null;
                $hasNumber = true;
                continue;
            }

            $token = 'vingts' === $token ? 'vingt' : $token;
            $value = self::UNITS[$token] ?? null;
            if (null === $value || (null !== $previous && $value >= $previous)) {
                return null;
            }
            $total += $value;
            $previous = $value;
            $hasNumber = true;
        }

        return $hasNumber ? (float) $total : null;
    }
}
