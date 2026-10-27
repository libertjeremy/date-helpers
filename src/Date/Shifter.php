<?php

declare(strict_types=1);

namespace LibertJeremy\DateHelpers\Date;

/**
 * Opérations sur une copie de la date reçue, qui conservent sa classe (DateTime, DateTimeImmutable ou sous-classe).
 *
 * @internal
 */
final class Shifter
{
    public static function add(\DateTimeInterface $date, \DateInterval $interval): \DateTimeInterface
    {
        if ($date instanceof \DateTimeImmutable) {
            return $date->add($interval);
        }

        return self::mutableCopy($date)->add($interval);
    }

    public static function sub(\DateTimeInterface $date, \DateInterval $interval): \DateTimeInterface
    {
        if ($date instanceof \DateTimeImmutable) {
            return $date->sub($interval);
        }

        return self::mutableCopy($date)->sub($interval);
    }

    public static function setTime(\DateTimeInterface $date, int $hour, int $minute, int $second): \DateTimeInterface
    {
        if ($date instanceof \DateTimeImmutable) {
            return $date->setTime($hour, $minute, $second);
        }

        return self::mutableCopy($date)->setTime($hour, $minute, $second);
    }

    private static function mutableCopy(\DateTimeInterface $date): \DateTime
    {
        if (!$date instanceof \DateTime) {
            throw new \LogicException(\sprintf('Unsupported date class %s.', $date::class));
        }

        return clone $date;
    }
}
