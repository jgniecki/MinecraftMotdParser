# Parsing
The Parser allows generating a `MotdItemCollection` object from the provided MOTD content.

## Table of Contents
1. [Introduction](parser.md#introduction)
2. [TextParser](parser.md#textparser)
3. [ArrayParser](parser.md#arrayparser)

## Introduction
Minecraft servers typically present MOTD in two formats: as formatted text following the [Minecraft Wiki](https://minecraft.fandom.com/wiki/Formatting_codes) description or in `JSON` format as a data map. Both formats are supported by this library. However, it's important to note that in its default configuration, the parsers do not support Bedrock Edition colors or custom formatting methods.

If you are not yet familiar with concepts like `FormatCollection` or `ColorCollection` and don’t know how to use them for formatting, you can learn more [here](formetter.md).

---

## TextParser
To parse MOTD saved in text format, use the `DevLancer\MinecraftMotdParser\Parser\TextParser` class:

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

The `$motdItemCollection` variable contains an instance of the `MotdItemCollection` class, with elements structured like this:

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "white", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

The parser correctly identifies the formatting elements. However, note that these two elements:

```php
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "white", 'text' => "craft "],
```

are very similar, differing only in the text value. To minimize data size, you can merge these elements:

```php
$motdItemCollection->mergeSimilarItem();
```

After applying this method, the resulting collection will look like this:

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Minecraft "],
    ['reset' => true, 'text' => "Server"],
]
```

---

## ArrayParser
To parse MOTD in JSON format (represented as an array), use the `DevLancer\MinecraftMotdParser\Parser\ArrayParser` class:

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

The resulting collection will look like this:

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "#FDDC5C", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

An additional feature of this parser is the support for custom colors written in HEX format, such as `#FDDC5C`. This color will be correctly stored in the collection element. However, it's important to note that without additional configuration, only the `HtmlGenerator` can meaningfully use these custom colors when generating content.

---