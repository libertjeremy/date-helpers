<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Parser;

/**
 * Moment de la journée évoqué à l'oral ("demain matin", "ce soir"...).
 */
enum TimeOfDay: string
{
    case MORNING = 'morning';
    case NOON = 'noon';
    case AFTERNOON = 'afternoon';
    case EVENING = 'evening';
    case NIGHT = 'night';

    /**
     * Reconnaît un mot français déjà normalisé (minuscules, sans accents) : matin, midi, apres-midi, soir, nuit.
     */
    public static function fromFrenchWord(string $word): ?self
    {
        return match (true) {
            str_contains($word, 'matin') => self::MORNING,
            str_contains($word, 'apres') => self::AFTERNOON,
            str_contains($word, 'midi') => self::NOON,
            str_contains($word, 'soir') => self::EVENING,
            str_contains($word, 'nuit') => self::NIGHT,
            default => null,
        };
    }
}
