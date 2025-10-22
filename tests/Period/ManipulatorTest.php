<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Period;

use LibertJeremy\DateHelpers\Period\Formatter;
use LibertJeremy\DateHelpers\Period\Manipulator;
use PHPUnit\Framework\TestCase;

class ManipulatorTest extends TestCase
{
    public function testTruncate1(): void
    {
        $truncatedDatePeriod = Formatter::fromDates(new \DateTime('2021-11-30'), new \DateTime('2022-01-30'));
        $referenceDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));
        $expectedDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));

        self::assertEquals($expectedDatePeriod, Manipulator::truncate($truncatedDatePeriod, $referenceDatePeriod));
    }

    public function testTruncate2(): void
    {
        $truncatedDatePeriod = Formatter::fromDates(new \DateTime('2021-11-30'), new \DateTime('2021-12-12'));
        $referenceDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));
        $expectedDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-12'));

        self::assertEquals($expectedDatePeriod, Manipulator::truncate($truncatedDatePeriod, $referenceDatePeriod));
    }

    public function testTruncate3(): void
    {
        $truncatedDatePeriod = Formatter::fromDates(new \DateTime('2021-12-08'), new \DateTime('2021-12-12'));
        $referenceDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));
        $expectedDatePeriod = Formatter::fromDates(new \DateTime('2021-12-08'), new \DateTime('2021-12-12'));

        self::assertEquals($expectedDatePeriod, Manipulator::truncate($truncatedDatePeriod, $referenceDatePeriod));
    }

    public function testTruncate4(): void
    {
        $truncatedDatePeriod = Formatter::fromDates(new \DateTime('2020-12-08'), new \DateTime('2020-12-12'));
        $referenceDatePeriod = Formatter::fromDates(new \DateTime('2021-12-01'), new \DateTime('2021-12-31'));

        self::assertNull(Manipulator::truncate($truncatedDatePeriod, $referenceDatePeriod));
    }

    public function testSplitNonOverlappingPeriods1(): void
    {
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-14'), true),
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-01-31'), true),
            Formatter::fromDates(new \DateTime('2025-02-01'), new \DateTime('2025-02-17'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true),
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-02-17'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods2(): void
    {
        // Cas : périodes déjà séparées, pas de chevauchement
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-15'), true),
            Formatter::fromDates(new \DateTime('2025-02-01'), new \DateTime('2025-02-15'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-15'), true),
            Formatter::fromDates(new \DateTime('2025-02-01'), new \DateTime('2025-02-15'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods3(): void
    {
        // Cas : une période est complètement contenue dans une autre
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-09'), true),
            Formatter::fromDates(new \DateTime('2025-01-10'), new \DateTime('2025-01-20'), true),
            Formatter::fromDates(new \DateTime('2025-01-21'), new \DateTime('2025-01-31'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true),
            Formatter::fromDates(new \DateTime('2025-01-10'), new \DateTime('2025-01-20'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods4(): void
    {
        // Cas : trois périodes qui se chevauchent
        // Période 1: 2025-01-01 -> 2025-01-31
        // Période 2: 2025-01-10 -> 2025-01-20
        // Période 3: 2025-01-15 -> 2025-01-19
        // Résultat attendu : toutes les dates sont couvertes par au moins la période 1
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-09'), true),
            Formatter::fromDates(new \DateTime('2025-01-10'), new \DateTime('2025-01-14'), true),
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-01-19'), true),
            Formatter::fromDates(new \DateTime('2025-01-20'), new \DateTime('2025-01-20'), true),
            Formatter::fromDates(new \DateTime('2025-01-21'), new \DateTime('2025-01-31'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true),
            Formatter::fromDates(new \DateTime('2025-01-10'), new \DateTime('2025-01-20'), true),
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-01-19'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods5(): void
    {
        // Cas : deux périodes identiques
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true),
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods6(): void
    {
        // Cas : tableau vide
        $expected = [];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([]));
    }

    public function testSplitNonOverlappingPeriods7(): void
    {
        // Cas : une seule période
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods8(): void
    {
        // Cas : périodes consécutives (fin d'une = début de l'autre - 1 jour)
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-15'), true),
            Formatter::fromDates(new \DateTime('2025-01-16'), new \DateTime('2025-01-31'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-15'), true),
            Formatter::fromDates(new \DateTime('2025-01-16'), new \DateTime('2025-01-31'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods9(): void
    {
        // Cas : plusieurs chevauchements complexes
        // Période 1: 2025-01-01 -> 2025-02-10
        // Période 2: 2025-01-05 -> 2025-01-25
        // Période 3: 2025-01-15 -> 2025-02-28
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-04'), true),
            Formatter::fromDates(new \DateTime('2025-01-05'), new \DateTime('2025-01-14'), true),
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-01-25'), true),
            Formatter::fromDates(new \DateTime('2025-01-26'), new \DateTime('2025-02-10'), true),
            Formatter::fromDates(new \DateTime('2025-02-11'), new \DateTime('2025-02-28'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-02-10'), true),
            Formatter::fromDates(new \DateTime('2025-01-05'), new \DateTime('2025-01-25'), true),
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-02-28'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods10(): void
    {
        // Cas : chevauchement partiel à la fin de la première période
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-19'), true),
            Formatter::fromDates(new \DateTime('2025-01-20'), new \DateTime('2025-01-31'), true),
            Formatter::fromDates(new \DateTime('2025-02-01'), new \DateTime('2025-02-10'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true),
            Formatter::fromDates(new \DateTime('2025-01-20'), new \DateTime('2025-02-10'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods11(): void
    {
        // Cas : périodes d'un seul jour qui se chevauchent
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-01-15'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-01-15'), true),
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-01-15'), true)
        ]));
    }

    public function testSplitNonOverlappingPeriods12(): void
    {
        // Cas : périodes fournies dans le désordre
        $expected = [
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-14'), true),
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-01-31'), true),
            Formatter::fromDates(new \DateTime('2025-02-01'), new \DateTime('2025-02-17'), true),
        ];

        self::assertEquals($expected, Manipulator::splitNonOverlappingPeriods([
            Formatter::fromDates(new \DateTime('2025-01-15'), new \DateTime('2025-02-17'), true),
            Formatter::fromDates(new \DateTime('2025-01-01'), new \DateTime('2025-01-31'), true)
        ]));
    }
}
