<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Parser;

/**
 * Résultat de {@see FrenchRelativeDateParser::parse()}.
 */
final class ParsedRelativeDate
{
    /**
     * @param \DateTimeImmutable $date       Jour reconnu, à minuit, dans le fuseau de la date de référence
     * @param TimeOfDay|null     $timeOfDay  Moment de la journée si le texte en mentionne un
     * @param string             $matched    Fragment du texte ORIGINAL reconnu (accents et casse d'origine), à retirer de la phrase
     * @param float              $confidence De 0 à 1 : 1 pour une expression non ambiguë, moins pour les expressions floues
     * @param int                $offset     Position en octets de $matched dans le texte original
     */
    public function __construct(
        public readonly \DateTimeImmutable $date,
        public readonly ?TimeOfDay $timeOfDay,
        public readonly string $matched,
        public readonly float $confidence,
        public readonly int $offset,
    ) {
    }
}
