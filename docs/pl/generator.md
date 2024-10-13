# Generator
Generator umożliwia generowanie treści Minecraft MOTD na podstawie `MotdItemCollection` oraz [kolekcji formatterów](formetter.md), w różnych formatach.

## Spis treści
1. [Wprowadzenie](generator.md#wprowadzenie)
2. [Generator HTML](generator.md#generator-html)
3. [Generator RAW MOTD](generator.md#generator-raw-motd)
4. [Generator czystego tekstu MOTD](generator.md#generator-czystego-tekstu-motd)

## Wprowadzenie
Zanim omówimy różne metody generowania treści, warto zauważyć, że każdy generator implementuje interfejs `DevLancer\MinecraftMotdParser\Collection\MotdItemCollection\GeneratorInterface`. W związku z tym, każdy generator posiada metodę `generate`, która przyjmuje jako argument `MotdItemCollection $collection`. Kolekcję `MotdItemCollection` można uzyskać m.in. poprzez parsowanie Minecraft MOTD za pomocą [parserów](parser.md). W tej dokumentacji używamy zmiennej `$motdItemCollection`, która zawiera instancję klasy `MotdItemCollection`. Elementy tej kolekcji mogą wyglądać tak:

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "dark_red", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

Jeśli nie jesteś jeszcze zaznajomiony z `FormatCollection` lub `ColorCollection` i chcesz dowiedzieć się, jak używać ich do formatowania, sprawdź szczegóły [tutaj](formetter.md).

---

## Generator HTML
Generowanie kodu HTML odbywa się za pomocą klasy `DevLancer\MinecraftMotdParser\Generator\HtmlGenerator`.

```php
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use DevLancer\MinecraftMotdParser\Generator\HtmlGenerator;

$formatCollection = FormatCollection::generate();
$colorCollection  = ColorCollection::generate();
$generator = new HtmlGenerator($formatCollection, $colorCollection);

echo $generator->generate($motdItemCollection); 
```

Warto zaznaczyć, że konstruktor przyjmuje opcjonalnie `$formatCollection` i `$colorCollection`. W przypadku ich braku zostaną one automatycznie wygenerowane.

#### Wynik
```html
A <span style="font-weight: bold; color: #FFFFFF;">Mine</span>
<span style="font-weight: bold; color: #AA0000;">craft </span> Server
```

Dodatkowo, ten generator obsługuje niestandardowe kolory zapisane w formacie HEX, nawet jeśli nie są one zawarte w kolekcji kolorów.

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "#FDDC5C", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

#### Wynik
```html
A <span style="font-weight: bold; color: #FFFFFF;">Mine</span>
<span style="font-weight: bold; color: #FDDC5C;">craft </span> Server
```

---

## Generator RAW MOTD
Do generowania Minecraft MOTD w postaci RAW (surowej) służy klasa `DevLancer\MinecraftMotdParser\Generator\RawGenerator`.

Konstruktor `RawGenerator` w trzecim parametrze przyjmuje znak sekcji, który poprzedza [klucz formattera](formetter.md#podstawowy-formatter). Domyślnym znakiem jest `§`.

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

**Uwaga:** Jeśli element w `$motdItemCollection` zawiera niestandardowy format, np. kolor, który nie występuje w kolekcji formatterów, generator pominie ten element podczas generowania.

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "#FDDC5C", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

#### Wynik
```text
A &f&lMinecraft &rServer
```

Ponieważ kolor `#FDDC5C` nie był zdefiniowany w kolekcji, został pominięty. Aby rozwiązać ten problem, możesz dodać niestandardowy [formatter dla koloru](formetter.md#gotowa-implementacja-kolorowego-formattera) do kolekcji `ColorCollection`.

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
Najprostszym sposobem na generowanie samego tekstu MOTD jest użycie klasy `DevLancer\MinecraftMotdParser\Generator\TextGenerator`.

```php
use DevLancer\MinecraftMotdParser\Generator\TextGenerator;

$generator = new TextGenerator();
echo $generator->generate($motdItemCollection);
```

#### Wynik
```text
A Minecraft Server
```