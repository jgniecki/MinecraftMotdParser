# Generator
Generator umożliwia na podstawie `MotdItemCollection` oraz [kolekcji formatterów](formetter.md) wygenerowanie w pożądanej postaci Minecraft MOTD.

## Spis treści
1. [Wprowadzenia](generator.md#wprowadzenie)
2. [Generator HTMLu](generator.md#generator-htmlu)
3. [Generator RAW MOTD](generator.md#generator-raw-motd)
4. [Generator czystego tekstu MOTD](generator.md#generator-czystego-tekstu-motd)

## Wprowadzenie
Zanim zostaną przedstawione sposoby generowania treści, warto zauważyć, że każdy generator implementuje interfejs 
`DevLancer\MinecraftMotdParser\Collection\MotdItemCollection\GeneratorInterface` dlatego każdy posiada metode `generate` która
przyjmuje jako argument `MotdItemCollection $collection` ([więcej tutaj](motdcollection.md)), jednym ze sposóbów uzyskania kolekcji
jest utworzenie jej na podstawie Minecraft MOTD za pomocą [parserów](parser.md), w dalszej częsci dokumentacji będę używał
zmiennej `$motdItemCollection` która zawiera instancje klasy `MotdItemCollection` której elementy kolecji można przedstawić w sposób:
```php
[
    ['text': "A "],
    ['bold': true, 'color': "white", 'text': "Mine"],
    ['bold': true, 'color': "dark_red", 'text': "craft "],
    ['reset': true, 'text': "Server"],
]
```
Jeżeli jeszcze nie wiesz czym jest `FormatCollection` lub `ColorCollection` i jak wykorzystać go w celu formatowania możesz przeczytać o tym zagadnieniu [tutaj](formetter.md).

---

## Generator HTMLu
Generowanie kodu HTML odbywa się za pomocą klasy `DevLancer\MinecraftMotdParser\Generator\HtmlGenerator`.
```php
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use DevLancer\MinecraftMotdParser\Generator\HtmlGenerator

$formatCollection = FormatCollection::generate();
$colorCollection  = ColorCollection::generate();
$generator = new HtmlGenerator($formatCollection, $colorCollection);

echo $generator->generate($motdItemCollection); 
```
Warto wspomnieć, że konstruktor przyjmuje opcjonalnie zmienne `$formatCollection` i `$colorCollection`, w przypadku ich braku
zostaną automatycznie wygenerowane.

#### Wynik
```html
A <span style="font-weight: bold; color: #FFFFFF;">Mine</span>
<span style="font-weight: bold; color: #AA0000;">craft </span> Server
```
Dodatkowym aspektem tego generatora jest fakt, że może on wyświetlić niestandardowe kolory w zapise HEX który nie znajduje się w kolekcji kolorów.
```php
[
    ['text': "A "],
    ['bold': true, 'color': "white", 'text': "Mine"],
    ['bold': true, 'color': "#FDDC5C", 'text': "craft "],
    ['reset': true, 'text': "Server"],
]
```

#### Wynik
```html
A <span style="font-weight: bold; color: #FFFFFF;">Mine</span>
<span style="font-weight: bold; color: #FDDC5C;">craft </span> Server
```

---

## Generator RAW MOTD
Generowanie Minecraft MOTD odbywa się za pomocą klasy `DevLancer\MinecraftMotdParser\Generator\RawGenerator`.

Konstruktor `RawGenerator` w trzecim parametrze przyjmuje znak sekcji, który poprzedza [klucz formattera](formetter.md#podstawowy-formatter)
domyślnie jest to `§`.

```php
use DevLancer\MinecraftMotdParser\Generator\RawGenerator;
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;

$formatCollection = FormatCollection::generate();
$colorCollection  = ColorCollection::generate();
$symbol = "&";
$generator = new RawGenerator($formatCollection, $colorCollection, $symbol);
echo $generator->generate($motdItemCollection);
```

#### Wynik
```text
A &f&lMine&4craft &rServer
```

**UWAGA** Jeżeli element w kolekcji `$motdItemCollection` zawiera niestandardowy sposób formatowania np. kolor (nie występuje w kolekcji formatterów), wyłącznie niestandardowe formatowanie zostanie pominięte przy generowaniu treści.

```php
[
    ['text': "A "],
    ['bold': true, 'color': "white", 'text': "Mine"],
    ['bold': true, 'color': "#FDDC5C", 'text': "craft "],
    ['reset': true, 'text': "Server"],
]
```

#### Wynik
```text
A &f&lMinecraft &rServer
```
Generator nie posiadał formatu dla koloru: #FDDC5C i go pominął, możesz utworzyć swój [formatter dla koloru](formetter.md#gotowa-implementacja-kolorowego-formattera) i dodać go do kolekcji `ColorCollection` aby rozwiązać ten problem.
```php
$light_gold = new DevLancer\MinecraftMotdParser\Formatter\ColorFormat('z', 'light_gold', '#FDDC5C');
$colorCollection->add($light_gold);
$generator = new RawGenerator($formatCollection, $colorCollection, $symbol);
echo $generator->generate($motdItemCollection);
```

#### Wynik
```text
A &f&lMine&zcraft &rServer
```

---

## Generator czystego tekstu MOTD
Najprostszy generator `DevLancer\MinecraftMotdParser\Generator\TextGenerator` który generuje wyłącznie tekst MOTD.

```php
use DevLancer\MinecraftMotdParser\Generator\TextGenerator;

$generator = new TextGenerator();
echo $generator->generate($motdItemCollection);
```

#### Wynik
```text
A Minecraft Server
```