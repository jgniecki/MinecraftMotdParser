# Minecraft MOTD Parser
![](https://img.shields.io/packagist/l/dev-lancer/minecraft-motd-parser?style=for-the-badge)
![](https://img.shields.io/packagist/dt/dev-lancer/minecraft-motd-parser?style=for-the-badge)
![](https://img.shields.io/github/v/release/jgniecki/MinecraftMotdParser?style=for-the-badge)
![](https://img.shields.io/packagist/php-v/dev-lancer/minecraft-motd-parser?style=for-the-badge)

PHP library to parse minecraft server motd

## Installation
This library can be installed by issuing the following command:
```bash
composer require dev-lancer/minecraft-motd-parser
```
## Parsing

### Usage TextParser
To parse a text-based MOTD using custom formatting and colors:

```php
$formatCollection = \DevLancer\MinecraftMotdParser\Collection\FormatCollection::generate();
$colorCollection  = \DevLancer\MinecraftMotdParser\Collection\ColorCollection::generate();
$parser = new \DevLancer\MinecraftMotdParser\Parser\TextParser($formatCollection, $colorCollection, '&');

$motd = "A &l&fMine&4craft &rServer";
$motdItemCollection = $parser->parse($motd, new \DevLancer\MinecraftMotdParser\Collection\MotdItemCollection());
```

### Usage ArrayParser
To parse a structured array-based MOTD:

```php
$formatCollection = \DevLancer\MinecraftMotdParser\Collection\FormatCollection::generate();
$colorCollection  = \DevLancer\MinecraftMotdParser\Collection\ColorCollection::generate();
$parser = new \DevLancer\MinecraftMotdParser\Parser\ArrayParser($formatCollection, $colorCollection);

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
                "color" => "dark_red",
                "text" => "craft "
            ],
        ]
    ],
    [
        "text" => "Server"
    ]
];
$motdItemCollection = $parser->parse($motd, new \DevLancer\MinecraftMotdParser\Collection\MotdItemCollection());
```

### Merging Similar Items
The mergeSimilarItem() method in the MotdItemCollection class merges adjacent MotdItem objects with the same formatting and color. This optimization helps to reduce redundancy in the formatting and color codes, making the MOTD more concise.

```php
$motd = "A &l&fMine&f&lcraft &rServer";
$motdItemCollection = $parser->parse($motd, new \DevLancer\MinecraftMotdParser\Collection\MotdItemCollection());

//Output before
[
    ['text': "A "],
    ['bold': true, 'color': "white", 'text': "Mine"],
    ['bold': true, 'color': "white", 'text': "craft "],
    ['reset': true, 'text': "Server"],
]

$motdItemCollection->mergeSimilarItem();
//Output after
[
    ['text': "A "],
    ['bold': true, 'color': "white", 'text': "Minecraft "],
    ['reset': true, 'text': "Server"],
]
```

## Generation

### MotdItemCollection
Example of creating a MotdItemCollection:

```php
$parser = new \DevLancer\MinecraftMotdParser\Parser\TextParser();
$motd = "A §l§fMine§4craft §rServer";
$motdItemCollection = $parser->parse($motd, new \DevLancer\MinecraftMotdParser\Collection\MotdItemCollection());
```

### Usage HtmlGenerator
To generate HTML from a parsed MOTD:
```php
$generator = new \DevLancer\MinecraftMotdParser\Generator\HtmlGenerator();

// Generate HTML from the MOTD item collection
echo $generator->generate($motdItemCollection); 
```

#### Output
The output will be:
```html
A <span style="font-weight: bold; color: #FFFFFF;">Mine</span>
<span style="font-weight: bold; color: #AA0000;">craft </span> Server
```

### Usage RawGenerator
To generate raw text from a parsed MOTD:
```php
$generator = new \DevLancer\MinecraftMotdParser\Generator\RawGenerator("§");
// Generate raw text from the MOTD item collection
echo $generator->generate($motdItemCollection); 
//output: A §f§lMine§4craft §rServer
```

### Usage TextGenerator
To generate plain text from a parsed MOTD:
```php
$generator = new \DevLancer\MinecraftMotdParser\Generator\TextGenerator();
// Generate plain text from the MOTD item collection
echo $generator->generate($motdItemCollection); 
//output: A Minecraft Server
```

## Custom formatter

### Define new class formatter
Example of creating a custom bold formatter:
```php
class CustomBoldFormatter implements FormatterInterface
{
    public function getKey(): string
    {
        return 'l';
    }

    public function getName(): string
    {
        return 'bold';
    }

    public function getFormat(): string
    {
        return '<b class="CustomBoldFormatter">%s</b>';
    }
}
```

### Usage
To use the custom formatter:

```php
// Create a new format collection
$formatCollection = \DevLancer\MinecraftMotdParser\Collection\FormatCollection::generate();

// and override the default formatter for bold
$formatCollection->add(new CustomBoldFormatter());

// Create a new MOTD item
$motdItem = new \DevLancer\MinecraftMotdParser\MotdItem();
$motdItem->setBold(true);
$motdItem->setText("Hello World");

// Create a new MOTD item collection and add the MOTD item
$motdItemCollection = new \DevLancer\MinecraftMotdParser\Collection\MotdItemCollection();
$motdItemCollection->add($motdItem);

// Generate HTML using the custom formatter
$generator = new \DevLancer\MinecraftMotdParser\Generator\HtmlGenerator($formatCollection);
echo $generator->generate($motdItemCollection); 
```

### Output
The output will be:
```html
<b class="CustomBoldFormatter">Hello World</b>
```

## License
This library is licensed under the [MIT](LICENSE) License.
