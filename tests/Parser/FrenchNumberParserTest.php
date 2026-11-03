<?php

declare(strict_types=1);

namespace LibertJeremy\Tests\DateHelpers\Parser;

use LibertJeremy\DateHelpers\Parser\FrenchNumberParser;
use PHPUnit\Framework\TestCase;

class FrenchNumberParserTest extends TestCase
{
    public function testUn(): void
    {
        self::assertEqualsWithDelta(1.0, FrenchNumberParser::parse('un'), 0.000001);
    }

    public function testUne(): void
    {
        self::assertEqualsWithDelta(1.0, FrenchNumberParser::parse('une'), 0.000001);
    }

    public function testTwo(): void
    {
        self::assertEqualsWithDelta(2.0, FrenchNumberParser::parse('deux'), 0.000001);
    }

    public function testTen(): void
    {
        self::assertEqualsWithDelta(10.0, FrenchNumberParser::parse('dix'), 0.000001);
    }

    public function testTwelve(): void
    {
        self::assertEqualsWithDelta(12.0, FrenchNumberParser::parse('douze'), 0.000001);
    }

    public function testSixteen(): void
    {
        self::assertEqualsWithDelta(16.0, FrenchNumberParser::parse('seize'), 0.000001);
    }

    public function testTwenty(): void
    {
        self::assertEqualsWithDelta(20.0, FrenchNumberParser::parse('vingt'), 0.000001);
    }

    public function testThirty(): void
    {
        self::assertEqualsWithDelta(30.0, FrenchNumberParser::parse('trente'), 0.000001);
    }

    public function testForty(): void
    {
        self::assertEqualsWithDelta(40.0, FrenchNumberParser::parse('quarante'), 0.000001);
    }

    public function testFifty(): void
    {
        self::assertEqualsWithDelta(50.0, FrenchNumberParser::parse('cinquante'), 0.000001);
    }

    public function testHundred(): void
    {
        self::assertEqualsWithDelta(100.0, FrenchNumberParser::parse('cent'), 0.000001);
    }

    public function testUpperCase(): void
    {
        self::assertEqualsWithDelta(20.0, FrenchNumberParser::parse('VINGT'), 0.000001);
    }

    public function testZero(): void
    {
        self::assertEqualsWithDelta(0.0, FrenchNumberParser::parse('zéro'), 0.000001);
    }

    public function testAccentInsensitive(): void
    {
        self::assertEqualsWithDelta(0.0, FrenchNumberParser::parse('zero'), 0.000001);
    }

    public function testCompoundWithHyphen(): void
    {
        self::assertEqualsWithDelta(22.0, FrenchNumberParser::parse('vingt-deux'), 0.000001);
    }

    public function testCompoundWithEt(): void
    {
        self::assertEqualsWithDelta(21.0, FrenchNumberParser::parse('vingt et un'), 0.000001);
    }

    public function testSeventyOne(): void
    {
        self::assertEqualsWithDelta(71.0, FrenchNumberParser::parse('soixante et onze'), 0.000001);
    }

    public function testEightyIsQuatreVingts(): void
    {
        self::assertEqualsWithDelta(80.0, FrenchNumberParser::parse('quatre-vingts'), 0.000001);
    }

    public function testNinetySeven(): void
    {
        self::assertEqualsWithDelta(97.0, FrenchNumberParser::parse('quatre-vingt-dix-sept'), 0.000001);
    }

    public function testTwoHundredThree(): void
    {
        self::assertEqualsWithDelta(203.0, FrenchNumberParser::parse('deux cent trois'), 0.000001);
    }

    public function testDozenSpoken(): void
    {
        self::assertEqualsWithDelta(12.0, FrenchNumberParser::parse('une douzaine'), 0.000001);
    }

    public function testHalfDozenWithHyphen(): void
    {
        self::assertEqualsWithDelta(6.0, FrenchNumberParser::parse('une demi-douzaine'), 0.000001);
    }

    public function testHalfDozenWithSpace(): void
    {
        self::assertEqualsWithDelta(6.0, FrenchNumberParser::parse('une demi douzaine'), 0.000001);
    }

    public function testQuarter(): void
    {
        self::assertEqualsWithDelta(0.25, FrenchNumberParser::parse('un quart'), 0.000001);
    }

    public function testHalfWithLa(): void
    {
        self::assertEqualsWithDelta(0.5, FrenchNumberParser::parse('la moitié'), 0.000001);
    }

    public function testHalfUnDemi(): void
    {
        self::assertEqualsWithDelta(0.5, FrenchNumberParser::parse('un demi'), 0.000001);
    }

    public function testHalfUneDemie(): void
    {
        self::assertEqualsWithDelta(0.5, FrenchNumberParser::parse('une demie'), 0.000001);
    }

    public function testThreeQuarters(): void
    {
        self::assertEqualsWithDelta(0.75, FrenchNumberParser::parse('trois quarts'), 0.000001);
    }

    public function testThreeQuartersWithArticle(): void
    {
        self::assertEqualsWithDelta(0.75, FrenchNumberParser::parse('les trois quarts'), 0.000001);
    }

    public function testThird(): void
    {
        self::assertEqualsWithDelta(1 / 3, FrenchNumberParser::parse('un tiers'), 0.000001);
    }

    public function testTwoThirds(): void
    {
        self::assertEqualsWithDelta(2 / 3, FrenchNumberParser::parse('deux tiers'), 0.000001);
    }

    public function testDigitsInteger(): void
    {
        self::assertEqualsWithDelta(12.0, FrenchNumberParser::parse('12'), 0.000001);
    }

    public function testDigitsWithCommaDecimal(): void
    {
        self::assertEqualsWithDelta(1.5, FrenchNumberParser::parse('1,5'), 0.000001);
    }

    public function testDigitsWithDotDecimal(): void
    {
        self::assertEqualsWithDelta(2.25, FrenchNumberParser::parse('2.25'), 0.000001);
    }

    public function testDigitsAreTrimmed(): void
    {
        self::assertEqualsWithDelta(7.0, FrenchNumberParser::parse('  7 '), 0.000001);
    }

    public function testUnknownWordReturnsNull(): void
    {
        self::assertNull(FrenchNumberParser::parse('chat'));
    }

    public function testEmptyStringReturnsNull(): void
    {
        self::assertNull(FrenchNumberParser::parse(''));
    }

    public function testOnlySpacesReturnsNull(): void
    {
        self::assertNull(FrenchNumberParser::parse('   '));
    }

    public function testTwoUnitsInARowReturnsNull(): void
    {
        self::assertNull(FrenchNumberParser::parse('trois quatre'));
    }

    public function testWordsMixedWithDigitsReturnsNull(): void
    {
        self::assertNull(FrenchNumberParser::parse('3 chats'));
    }
}
