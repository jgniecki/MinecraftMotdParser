# Parsowanie
Parser umożliwia generowanie obiektu `MotdItemCollection` na podstawie dostarczonej treści MOTD.

## Spis treści
1. [Wprowadzenie](parser.md#wprowadzenie)
2. [TextParser](parser.md#textparser)
3. [ArrayParser](parser.md#arrayparser)

## Wprowadzenie
Serwery Minecraft najczęściej prezentują MOTD w dwóch formatach: w postaci sformatowanego tekstu zgodnego z opisem na [Minecraft Wiki](https://minecraft.fandom.com/wiki/Formatting_codes) lub w formacie `JSON` jako mapa danych. Oba te formaty są obsługiwane przez bibliotekę. Należy jednak pamiętać, że w domyślnej konfiguracji parsery nie obsługują kolorów BE ani niestandardowych metod formatowania.

Jeśli nie jesteś jeszcze zaznajomiony z pojęciami takimi jak `FormatCollection` lub `ColorCollection` oraz nie wiesz, jak ich używać do formatowania, możesz dowiedzieć się więcej [tutaj](formetter.md).

---

## TextParser
Aby analizować MOTD zapisane w formie tekstowej, użyj klasy `DevLancer\MinecraftMotdParser\Parser\TextParser`:

```php
use DevLancer\MinecraftMotdParser\Parser\TextParser;
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;

$formatCollection = FormatCollection::generate();
$colorCollection  = ColorCollection::generate();
$parser = new TextParser($formatCollection, $colorCollection, '&');

$motd = "A &l&fMine&fcraft &rServer";
$motdItemCollection = $parser->parse($motd, new MotdItemCollection());
```

Zmienna `$motdItemCollection` zawiera instancję klasy `MotdItemCollection`, której elementy będą miały następującą strukturę:

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "white", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

Parser poprawnie rozpoznaje poszczególne elementy formatujące. Zauważ jednak, że elementy:

```php
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "white", 'text' => "craft "],
```

są bardzo podobne do siebie i różnią się jedynie wartością tekstu. Aby zminimalizować ilość danych, można połączyć te elementy:

```php
$motdItemCollection->mergeSimilarItem();
```

Po zastosowaniu tej metody wynikowa kolekcja będzie wyglądała tak:

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Minecraft "],
    ['reset' => true, 'text' => "Server"],
]
```

---

## ArrayParser
Aby analizować MOTD w formacie JSON (reprezentowanym jako tablica), należy użyć klasy `DevLancer\MinecraftMotdParser\Parser\ArrayParser`:

```php
use DevLancer\MinecraftMotdParser\Parser\ArrayParser;
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;

$formatCollection = FormatCollection::generate();
$colorCollection  = ColorCollection::generate();
$parser = new ArrayParser($formatCollection, $colorCollection);

$motd = [
    [ "text" => "A "],
    [
        "bold" => true,
        "extra" => [
            [
                "color" => "white",
                "text" => "Mine"
            ],
            [
                "color" => "#FDDC5C",
                "text" => "craft "
            ],
        ]
    ],
    [
        "text" => "Server"
    ]
];
$motdItemCollection = $parser->parse($motd, new MotdItemCollection());
```

Wynikowa kolekcja będzie wyglądać następująco:

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "#FDDC5C", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

Dodatkową funkcją tego parsera jest obsługa niestandardowych kolorów zapisanych w formacie HEX, jak na przykład `#FDDC5C`. Kolor ten zostanie prawidłowo zapisany w elemencie kolekcji. Należy jednak pamiętać, że bez dodatkowej konfiguracji tylko generator `HtmlGenerator` może sensownie wykorzystać te niestandardowe kolory podczas generowania treści.

---