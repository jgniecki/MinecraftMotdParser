# Parsing
Parser umożliwia na podstawie dostarczonej treści wygenerować `MotdItemCollection`

## Spis treści
1. [Wprowadzenia](parser.md#wprowadzenie)
2. [TextParser](parser.md#textparser)
3. [ArrayParser](parser.md#arrayparser)

## Wprowadzenie
Najpopularniejszymi sposobami przedstawiania przez sewery Minecraft Motd jest zapis tekstowy zgodny z zapisem na [minecraft wiki](https://minecraft.fandom.com/wiki/Formatting_codes)
lub motd zapisany w sposób zgodny z formatem `JSON` w postaci mapy. Oba przypadki są obsługiwane, trzeba jednak pamiętać, że
w domyślnej konfiguracji nie są analizowane kolory BE bądź niestandardowe sposoby formatowania.

Jeżeli jeszcze nie wiesz czym jest `FormatCollection` lub `ColorCollection` i jak wykorzystać go w celu formatowania możesz przeczytać o tym zagadnieniu [tutaj](formetter.md).

---

## TextParser
Do analizowania tekstowego motd należy wykorzystać klase `DevLancer\MinecraftMotdParser\Parser\TextParser`.
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

Zmienna `$motdItemCollection` przyjęła instancje klasy `MotdItemCollection` i jej elementy można zapisać w sposób:
```php
[
    ['text': "A "],
    ['bold': true, 'color': "white", 'text': "Mine"],
    ['bold': true, 'color': "white", 'text': "craft "],
    ['reset': true, 'text': "Server"],
]
```

Parser poprawnie wykonał swoją pracę, ale zauważ, że elementy
```php
//...
    ['bold': true, 'color': "white", 'text': "Mine"],
    ['bold': true, 'color': "white", 'text': "craft "],
//...
```
są podobne do siebie, różni ich tylko pole `text`, w celu minimalizacji danych możesz te elementy połączyć
```php
$motdItemCollection->mergeSimilarItem();
```
Teraz wynik kolekcji będzie:
```php
[
    ['text': "A "],
    ['bold': true, 'color': "white", 'text': "Minecraft "],
    ['reset': true, 'text': "Server"],
]
```

---

## ArrayParser
Do analizowania motd w postaci tablicy należy wykorzystać klase `DevLancer\MinecraftMotdParser\Parser\ArrayParser`.
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

Wynikiem kolekcji będzie:
```php
[
    ['text': "A "],
    ['bold': true, 'color': "white", 'text': "Mine"],
    ['bold': true, 'color': "#FDDC5C", 'text': "craft "],
    ['reset': true, 'text': "Server"],
]
```

Dodatkowym aspektem tego parsera jest fakt, że może on przyjmować niestandardowe kolory w zapisie HEX,
kolor `#FDDC5C` zostanie zapisany w elemencie kolekcji, jednak należy pamiętać, że bez dodatkowej konfiguracji tylko
generator `HtmlGenerator` może wykorzystać ten aspekt w sensowny sposób podczas generowania treści.