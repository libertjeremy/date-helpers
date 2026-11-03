<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Parser;

use LibertJeremy\DateHelpers\Parser\FrenchRelativeDateParser;
use LibertJeremy\DateHelpers\Parser\ParsedRelativeDate;
use LibertJeremy\DateHelpers\Parser\TimeOfDay;
use PHPUnit\Framework\TestCase;

/**
 * Jour de référence : mercredi 4 novembre 2026, 15 h (Europe/Luxembourg).
 */
class FrenchRelativeDateParserTest extends TestCase
{
    public function testTodayWithApostrophe(): void
    {
        $parsed = $this->parse('Aujourd\'hui');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('Aujourd\'hui', $parsed->matched);
    }

    public function testTodayWithTypographicApostropheAndCapital(): void
    {
        $parsed = $this->parse('Aujourd’hui');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('Aujourd’hui', $parsed->matched);
    }

    public function testThisEveningIsTodayInTheEvening(): void
    {
        $parsed = $this->parse('on se voit ce soir');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::EVENING, $parsed->timeOfDay);
        self::assertSame('ce soir', $parsed->matched);
    }

    public function testThisMorningIsTodayInTheMorning(): void
    {
        $parsed = $this->parse('ce matin');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::MORNING, $parsed->timeOfDay);
        self::assertSame('ce matin', $parsed->matched);
    }

    public function testThisAfternoonIsAccentInsensitive(): void
    {
        $parsed = $this->parse('cet après-midi');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::AFTERNOON, $parsed->timeOfDay);
        self::assertSame('cet après-midi', $parsed->matched);
    }

    public function testThisAfternoonWithoutAccents(): void
    {
        $parsed = $this->parse('cet apres midi');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::AFTERNOON, $parsed->timeOfDay);
        self::assertSame('cet apres midi', $parsed->matched);
    }

    public function testThisNightIsTodayAtNight(): void
    {
        $parsed = $this->parse('cette nuit');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::NIGHT, $parsed->timeOfDay);
        self::assertSame('cette nuit', $parsed->matched);
    }

    public function testTomorrowAlone(): void
    {
        $parsed = $this->parse('rappelle-moi demain');

        self::assertSame('2026-11-05', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('demain', $parsed->matched);
    }

    public function testTomorrowMorning(): void
    {
        $parsed = $this->parse('demain matin');

        self::assertSame('2026-11-05', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::MORNING, $parsed->timeOfDay);
        self::assertSame('demain matin', $parsed->matched);
    }

    public function testTomorrowNoon(): void
    {
        $parsed = $this->parse('demain midi');

        self::assertSame('2026-11-05', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::NOON, $parsed->timeOfDay);
        self::assertSame('demain midi', $parsed->matched);
    }

    public function testTomorrowAfternoon(): void
    {
        $parsed = $this->parse('demain après-midi');

        self::assertSame('2026-11-05', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::AFTERNOON, $parsed->timeOfDay);
        self::assertSame('demain après-midi', $parsed->matched);
    }

    public function testTomorrowEvening(): void
    {
        $parsed = $this->parse('demain soir');

        self::assertSame('2026-11-05', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::EVENING, $parsed->timeOfDay);
        self::assertSame('demain soir', $parsed->matched);
    }

    public function testDayAfterTomorrowWithAccentAndHyphen(): void
    {
        $parsed = $this->parse('après-demain');

        self::assertSame('2026-11-06', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('après-demain', $parsed->matched);
    }

    public function testDayAfterTomorrowWithoutAccent(): void
    {
        $parsed = $this->parse('apres demain');

        self::assertSame('2026-11-06', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('apres demain', $parsed->matched);
    }

    public function testDayAfterTomorrowEvening(): void
    {
        $parsed = $this->parse('après-demain soir');

        self::assertSame('2026-11-06', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::EVENING, $parsed->timeOfDay);
        self::assertSame('après-demain soir', $parsed->matched);
    }

    public function testBareWeekdayIsNextOccurrenceAfterToday(): void
    {
        $parsed = $this->parse('lundi');

        self::assertSame('2026-11-09', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('lundi', $parsed->matched);
    }

    public function testBareWeekdayEqualToTodayIsNextWeek(): void
    {
        $parsed = $this->parse('mercredi');

        self::assertSame('2026-11-11', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('mercredi', $parsed->matched);
    }

    public function testWeekdayProchainEqualsBareWeekday(): void
    {
        $parsed = $this->parse('vendredi prochain');

        self::assertSame('2026-11-06', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('vendredi prochain', $parsed->matched);
    }

    public function testThisWeekdayIncludesToday(): void
    {
        $parsed = $this->parse('ce mercredi');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('ce mercredi', $parsed->matched);
    }

    public function testThisWeekdayLaterInTheWeek(): void
    {
        $parsed = $this->parse('ce vendredi');

        self::assertSame('2026-11-06', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('ce vendredi', $parsed->matched);
    }

    public function testWeekdayWithEveningPeriod(): void
    {
        $parsed = $this->parse('samedi soir');

        self::assertSame('2026-11-07', $parsed->date->format('Y-m-d'));
        self::assertSame(TimeOfDay::EVENING, $parsed->timeOfDay);
        self::assertSame('samedi soir', $parsed->matched);
    }

    public function testWeekdayIsCaseInsensitive(): void
    {
        $parsed = $this->parse('DIMANCHE');

        self::assertSame('2026-11-08', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('DIMANCHE', $parsed->matched);
    }

    public function testInDigitsDays(): void
    {
        $parsed = $this->parse('dans 3 jours');

        self::assertSame('2026-11-07', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('dans 3 jours', $parsed->matched);
    }

    public function testInWordDays(): void
    {
        $parsed = $this->parse('dans deux jours');

        self::assertSame('2026-11-06', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('dans deux jours', $parsed->matched);
    }

    public function testInCompoundWordDays(): void
    {
        $parsed = $this->parse('dans vingt-deux jours');

        self::assertSame('2026-11-26', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('dans vingt-deux jours', $parsed->matched);
    }

    public function testInOneDaySingular(): void
    {
        $parsed = $this->parse('dans un jour');

        self::assertSame('2026-11-05', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('dans un jour', $parsed->matched);
    }

    public function testInOneWeek(): void
    {
        $parsed = $this->parse('dans une semaine');

        self::assertSame('2026-11-11', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('dans une semaine', $parsed->matched);
    }

    public function testInTwoWeeksWithDigits(): void
    {
        $parsed = $this->parse('dans 2 semaines');

        self::assertSame('2026-11-18', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('dans 2 semaines', $parsed->matched);
    }

    public function testInOneMonth(): void
    {
        $parsed = $this->parse('dans un mois');

        self::assertSame('2026-12-04', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('dans un mois', $parsed->matched);
    }

    public function testInMonthsAcrossYearEnd(): void
    {
        $parsed = $this->parse('dans trois mois');

        self::assertSame('2027-02-04', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('dans trois mois', $parsed->matched);
    }

    public function testNextWeekIsNextMonday(): void
    {
        $parsed = $this->parse('la semaine prochaine');

        self::assertSame('2026-11-09', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('la semaine prochaine', $parsed->matched);
    }

    public function testEndOfWeekIsComingSaturday(): void
    {
        $parsed = $this->parse('fin de semaine');

        self::assertSame('2026-11-07', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('fin de semaine', $parsed->matched);
    }

    public function testBareDayOfMonthLaterThisMonth(): void
    {
        $parsed = $this->parse('le 12');

        self::assertSame('2026-11-12', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('le 12', $parsed->matched);
    }

    public function testBareDayOfMonthAlreadyPassedMovesToNextMonth(): void
    {
        $parsed = $this->parse('le 2');

        self::assertSame('2026-12-02', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('le 2', $parsed->matched);
    }

    public function testBareDayOfMonthEqualToTodayIsToday(): void
    {
        $parsed = $this->parse('le 4');

        self::assertSame('2026-11-04', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('le 4', $parsed->matched);
    }

    public function testBareDayOfMonthMissingInNextMonthSkipsIt(): void
    {
        $parsed = $this->parse('le 31');

        self::assertSame('2026-12-31', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('le 31', $parsed->matched);
    }

    public function testDayAndMonthLaterThisYear(): void
    {
        $parsed = $this->parse('le 12 décembre');

        self::assertSame('2026-12-12', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('le 12 décembre', $parsed->matched);
    }

    public function testDayAndMonthAlreadyPassedMovesToNextYear(): void
    {
        $parsed = $this->parse('le 12 mars');

        self::assertSame('2027-03-12', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('le 12 mars', $parsed->matched);
    }

    public function testFirstOfMonthWithOrdinal(): void
    {
        $parsed = $this->parse('le 1er janvier');

        self::assertSame('2027-01-01', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('le 1er janvier', $parsed->matched);
    }

    public function testDayAndMonthWithExplicitYear(): void
    {
        $parsed = $this->parse('le 12 mars 2030');

        self::assertSame('2030-03-12', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('le 12 mars 2030', $parsed->matched);
    }

    public function testNumericDayMonthMovesToNextYear(): void
    {
        $parsed = $this->parse('12/03');

        self::assertSame('2027-03-12', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('12/03', $parsed->matched);
    }

    public function testNumericDayMonthLaterThisYear(): void
    {
        $parsed = $this->parse('25/12');

        self::assertSame('2026-12-25', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('25/12', $parsed->matched);
    }

    public function testNumericFullDate(): void
    {
        $parsed = $this->parse('12/03/2027');

        self::assertSame('2027-03-12', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('12/03/2027', $parsed->matched);
    }

    public function testNumericDateWithTwoDigitYear(): void
    {
        $parsed = $this->parse('12/03/28');

        self::assertSame('2028-03-12', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('12/03/28', $parsed->matched);
    }

    public function testLeapDayWaitsForTheNextLeapYear(): void
    {
        $parsed = $this->parse('29/02');

        self::assertSame('2028-02-29', $parsed->date->format('Y-m-d'));
        self::assertNull($parsed->timeOfDay);
        self::assertSame('29/02', $parsed->matched);
    }

    public function testParsedDateIsMidnightInTheTimezoneOfTheReference(): void
    {
        $parsed = $this->parse('demain');

        self::assertSame('2026-11-05 00:00:00 Europe/Luxembourg', $parsed->date->format('Y-m-d H:i:s e'));
    }

    public function testTextWithoutDateReturnsNull(): void
    {
        self::assertNull((new FrenchRelativeDateParser())->parse('acheter du pain', $this->now()));
    }

    public function testEmptyTextReturnsNull(): void
    {
        self::assertNull((new FrenchRelativeDateParser())->parse('', $this->now()));
    }

    public function testImpossibleCalendarDateReturnsNull(): void
    {
        self::assertNull((new FrenchRelativeDateParser())->parse('31/02', $this->now()));
    }

    public function testNonIntegerAmountIsNotADelay(): void
    {
        self::assertNull((new FrenchRelativeDateParser())->parse('dans 1,5 jours', $this->now()));
    }

    public function testHourIsNotADayOfMonth(): void
    {
        self::assertNull((new FrenchRelativeDateParser())->parse('le 12h30', $this->now()));
    }

    public function testMatchedFragmentLetsTheCallerStripItFromTheSentence(): void
    {
        $text = 'Appeler Paul demain matin pour le devis';
        $parsed = $this->parse($text);

        self::assertSame('Appeler Paul  pour le devis', substr_replace($text, '', $parsed->offset, \strlen($parsed->matched)));
    }

    public function testOffsetIsExpressedInBytesOfTheOriginalTextEvenWithAccents(): void
    {
        $text = 'Réunion à midi, après-demain';
        $parsed = $this->parse($text);

        self::assertSame('après-demain', substr($text, $parsed->offset, \strlen($parsed->matched)));
        self::assertSame('2026-11-06', $parsed->date->format('Y-m-d'));
    }

    public function testEarliestExpressionInTheSentenceWins(): void
    {
        $parsed = $this->parse('demain ou lundi');

        self::assertSame('2026-11-05', $parsed->date->format('Y-m-d'));
    }

    public function testLongestExpressionWinsOverItsPrefix(): void
    {
        $parsed = $this->parse('le 12 mars');

        self::assertSame('le 12 mars', $parsed->matched);
    }

    public function testEarlierUnrelatedDansDoesNotHideTheDelay(): void
    {
        $parsed = $this->parse('dans ma vie dans 3 jours');

        self::assertSame('dans 3 jours', $parsed->matched);
        self::assertSame('2026-11-07', $parsed->date->format('Y-m-d'));
    }

    public function testConfidenceIsFullForUnambiguousExpression(): void
    {
        self::assertSame(1.0, $this->parse('dans 3 jours')->confidence);
    }

    public function testConfidenceIsLowerForBareWeekday(): void
    {
        self::assertSame(0.9, $this->parse('lundi')->confidence);
    }

    public function testConfidenceIsLowerForBareDayOfMonth(): void
    {
        self::assertSame(0.7, $this->parse('le 12')->confidence);
    }

    public function testConfidenceIsLowestForFuzzyWeekExpressions(): void
    {
        self::assertSame(0.6, $this->parse('la semaine prochaine')->confidence);
    }

    public function testConfidenceIsFullForNumericDateWithYear(): void
    {
        self::assertSame(1.0, $this->parse('12/03/2027')->confidence);
    }

    public function testMonthDelayClampsToTheEndOfShorterMonths(): void
    {
        $now = new \DateTimeImmutable('2026-01-31 23:30:00', new \DateTimeZone('Europe/Luxembourg'));
        $parsed = (new FrenchRelativeDateParser())->parse('dans un mois', $now);

        self::assertNotNull($parsed);
        self::assertSame('2026-02-28', $parsed->date->format('Y-m-d'));
    }

    private function parse(string $text): ParsedRelativeDate
    {
        $parsed = (new FrenchRelativeDateParser())->parse($text, $this->now());
        self::assertNotNull($parsed, \sprintf('"%s" should be recognised.', $text));

        return $parsed;
    }

    private function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-11-04 15:00:00', new \DateTimeZone('Europe/Luxembourg'));
    }
}
