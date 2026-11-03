# libertjeremy/date-helpers

Helpers de dates et de périodes (`LibertJeremy\DateHelpers`).

## Dates relatives en français

Toutes ces API reçoivent explicitement la date de référence (`$now`) : l'horloge système n'est jamais lue.
Les dates retournées sont à minuit, dans le fuseau de `$now`.

### `Relative\DaysRemaining::between(DateTimeImmutable $date, DateTimeImmutable $now): int`

Jours calendaires entre `$now` et `$date` (0 le même jour, négatif dans le passé). Insensible aux heures,
aux changements d'heure et au fuseau de `$date`.

### `Relative\FrenchRelativeLabel::forDate(DateTimeImmutable $date, DateTimeImmutable $now): string`

Échéance lue comme un compte à rebours (N = jours restants) :

| N | Libellé |
|---|---|
| 0 | `Aujourd'hui` |
| 1 | `Demain` |
| 2 à 13 | `J-N` |
| 14 à 59 | `Dans N sem.` (N / 7, arrondi à l'inférieur) |
| >= 60 | `Dans N mois` (N / 30, arrondi à l'inférieur) |
| -1 | `Hier` |
| <= -2 | `Il y a N j` |

### `Relative\NextWeekday::after(DateTimeImmutable $now, int $isoWeekday, bool $includeToday = false): DateTimeImmutable`

Prochain jour ISO (1 = lundi ... 7 = dimanche), strictement après `$now` sauf `$includeToday`.
`InvalidArgumentException` hors de 1..7.

### `Parser\FrenchRelativeDateParser::parse(string $text, DateTimeImmutable $now): ?ParsedRelativeDate`

Repère une date relative dans une phrase (minuscules ou non, accents facultatifs) : `aujourd'hui`, `ce matin`,
`ce soir`, `demain` (+ `matin`/`midi`/`après-midi`/`soir`), `après-demain`, un jour de semaine, `dans 3 jours`,
`dans deux semaines`, `dans un mois`, `la semaine prochaine`, `fin de semaine`, `le 12`, `le 12 mars`, `12/03`, `12/03/2027`.

- Jour de semaine seul ou `lundi prochain` : prochaine occurrence **strictement après** aujourd'hui ; `ce lundi` : aujourd'hui compris.
- `la semaine prochaine` : le lundi suivant ; `fin de semaine` : le samedi à venir (aujourd'hui compris).
- `le 12`, `le 12 mars`, `12/03` : prochaine occurrence, aujourd'hui compris ; avec une année, la date exacte.
- Retourne `null` si rien n'est reconnu.

`ParsedRelativeDate` (immuable) : `date`, `timeOfDay` (`TimeOfDay::MORNING|NOON|AFTERNOON|EVENING|NIGHT` ou `null`),
`matched` (fragment du texte original), `confidence` (0 à 1) et `offset` (position en octets de `matched`),
de quoi retirer l'expression de la phrase :

```php
$parsed = (new FrenchRelativeDateParser())->parse('Appeler Paul demain matin', $now);
$rest = substr_replace($text, '', $parsed->offset, strlen($parsed->matched));
```

### `Parser\FrenchNumberParser::parse(string $word): ?float`

Nombres et fractions du français parlé : `un`...`vingt`, `trente`, `cent`, composés (`vingt-deux`, `quatre-vingt-dix-sept`),
`une douzaine` (12), `une demi-douzaine` (6), `un quart` (0,25), `la moitié` / `un demi` / `une demie` (0,5),
`trois quarts` (0,75), `un tiers`, et les chiffres avec virgule (`1,5`). `null` sinon.

## Qualité

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpstan analyse   # niveau 10
```
