<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Parser;

/**
 * Normalisation du français (minuscules, sans accents) qui conserve la correspondance des positions.
 *
 * @internal
 */
final class FrenchText
{
    private const FOLDING = [
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'À' => 'a', 'Â' => 'a', 'Ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'É' => 'e', 'È' => 'e', 'Ê' => 'e', 'Ë' => 'e',
        'î' => 'i', 'ï' => 'i', 'Î' => 'i', 'Ï' => 'i',
        'ô' => 'o', 'ö' => 'o', 'Ô' => 'o', 'Ö' => 'o',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'Ù' => 'u', 'Û' => 'u', 'Ü' => 'u',
        'ç' => 'c', 'Ç' => 'c', 'ÿ' => 'y',
        'œ' => 'oe', 'Œ' => 'oe', 'æ' => 'ae', 'Æ' => 'ae',
        '’' => "'", '‘' => "'", "\u{00A0}" => ' ', "\u{202F}" => ' ',
    ];

    public static function normalize(string $text): string
    {
        return self::normalizeWithOffsets($text)[0];
    }

    /**
     * @return array{0: string, 1: list<int>} Le texte normalisé et, pour chaque octet de celui-ci (plus la fin), la position de l'octet de départ du caractère d'origine
     */
    public static function normalizeWithOffsets(string $text): array
    {
        $characters = preg_split('//u', $text, -1, \PREG_SPLIT_NO_EMPTY);
        if (false === $characters) {
            $characters = str_split($text);
        }

        $normalized = '';
        $offsets = [];
        $position = 0;
        foreach ($characters as $character) {
            $replacement = self::FOLDING[$character] ?? strtolower($character);
            for ($index = 0, $length = \strlen($replacement); $index < $length; ++$index) {
                $offsets[] = $position;
            }
            $normalized .= $replacement;
            $position += \strlen($character);
        }
        $offsets[] = $position;

        return [$normalized, $offsets];
    }
}
