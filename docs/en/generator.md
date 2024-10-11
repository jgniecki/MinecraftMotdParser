# Generator
The Generator allows the generation of Minecraft MOTD content based on `MotdItemCollection` and [formatter collections](formetter.md) in various formats.

## Table of Contents
1. [Introduction](generator.md#introduction)
2. [HTML Generator](generator.md#html-generator)
3. [RAW MOTD Generator](generator.md#raw-motd-generator)
4. [Plain Text MOTD Generator](generator.md#plain-text-motd-generator)

## Introduction
Before discussing the various methods of generating content, it’s important to note that each generator implements the `DevLancer\MinecraftMotdParser\Collection\MotdItemCollection\GeneratorInterface`. Therefore, each generator has a `generate` method that accepts a `MotdItemCollection $collection` as an argument. The `MotdItemCollection` can be obtained by parsing Minecraft MOTD using [parsers](parser.md). In this documentation, we use the variable `$motdItemCollection`, which contains an instance of the `MotdItemCollection` class. The elements of this collection might look like this:

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "dark_red", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

If you're not yet familiar with `FormatCollection` or `ColorCollection` and want to learn how to use them for formatting, check the details [here](formetter.md).

---

## HTML Generator
HTML generation is done using the `DevLancer\MinecraftMotdParser\Generator\HtmlGenerator` class.

```php
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use DevLancer\MinecraftMotdParser\Generator\HtmlGenerator;

$formatCollection = FormatCollection::generate();
$colorCollection  = ColorCollection::generate();
$generator = new HtmlGenerator($formatCollection, $colorCollection);

echo $generator->generate($motdItemCollection); 
```

It’s important to note that the constructor optionally accepts `$formatCollection` and `$colorCollection`. If they are not provided, they will be automatically generated.

#### Output
```html
A <span style="font-weight: bold; color: #FFFFFF;">Mine</span>
<span style="font-weight: bold; color: #AA0000;">craft </span> Server
```

Additionally, this generator supports custom colors written in HEX format, even if they are not included in the color collection.

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "#FDDC5C", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

#### Output
```html
A <span style="font-weight: bold; color: #FFFFFF;">Mine</span>
<span style="font-weight: bold; color: #FDDC5C;">craft </span> Server
```

---

## RAW MOTD Generator
To generate Minecraft MOTD in RAW format, use the `DevLancer\MinecraftMotdParser\Generator\RawGenerator` class.

The `RawGenerator` constructor accepts a section symbol as the third parameter, which precedes the [formatter key](formetter.md#basic-formatter). The default symbol is `§`.

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

#### Output
```text
A &f&lMine&4craft &rServer
```

**Note:** If an element in the `$motdItemCollection` contains a custom format (e.g., a color not present in the formatter collection), the generator will skip that element during generation.

```php
[
    ['text' => "A "],
    ['bold' => true, 'color' => "white", 'text' => "Mine"],
    ['bold' => true, 'color' => "#FDDC5C", 'text' => "craft "],
    ['reset' => true, 'text' => "Server"],
]
```

#### Output
```text
A &f&lMinecraft &rServer
```

Since the color `#FDDC5C` was not defined in the collection, it was skipped. To fix this issue, you can add a custom [color formatter](formetter.md#ready-color-formatter-implementation) to the `ColorCollection`.

```php
$light_gold = new DevLancer\MinecraftMotdParser\Formatter\ColorFormat('z', 'light_gold', '#FDDC5C');
$colorCollection->add($light_gold);
$generator = new RawGenerator($formatCollection, $colorCollection, $symbol);

echo $generator->generate($motdItemCollection);
```

#### Output
```text
A &f&lMine&zcraft &rServer
```

---

## Plain Text MOTD Generator
The simplest way to generate plain MOTD text is by using the `DevLancer\MinecraftMotdParser\Generator\TextGenerator` class.

```php
use DevLancer\MinecraftMotdParser\Generator\TextGenerator;

$generator = new TextGenerator();
echo $generator->generate($motdItemCollection);
```

#### Output
```text
A Minecraft Server
```

---