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
```php
$formatCollection = \DevLancer\MinecraftMotdParser\FormatCollection::generate();
$colorCollection  = \DevLancer\MinecraftMotdParser\ColorCollection::generate();
$parser = new \DevLancer\MinecraftMotdParser\Parser\TextParser($formatCollection, $colorCollection, '&');

$motd = "A &l&fMine&4craft &rServer";
$motdItemCollection = $parser->parse($motd, new \DevLancer\MinecraftMotdParser\MotdItemCollection());
```

### Usage ArrayParser
```php
$colorCollection  = \DevLancer\MinecraftMotdParser\ColorCollection::generate();
$parser = new \DevLancer\MinecraftMotdParser\Parser\ArrayParser($colorCollection);

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
$motdItemCollection = $parser->parse($motd, new \DevLancer\MinecraftMotdParser\MotdItemCollection());
```

## Generation

### MotdItemCollection
```php
$parser = new \DevLancer\MinecraftMotdParser\Parser\TextParser();
$motd = "A &l&fMine&4craft &rServer";
$motdItemCollection = $parser->parse($motd, new \DevLancer\MinecraftMotdParser\MotdItemCollection());
```

### Usage HtmlGenerator
```php
$generator = new \DevLancer\MinecraftMotdParser\Generator\HtmlGenerator();
echo $generator->generate($motdItemCollection); 
```

#### Output
```html
A <span style="font-weight: bold; color: #FFFFFF;">Mine</span>
<span style="font-weight: bold; color: #AA0000;">craft </span> Server
```

### Usage RawGenerator
```php
$generator = new \DevLancer\MinecraftMotdParser\Generator\RawGenerator("&");
echo $generator->generate($motdItemCollection); 
//output: A &f&lMine&4craft &rServer
```

### Usage TextGenerator
```php
$generator = new \DevLancer\MinecraftMotdParser\Generator\TextGenerator();
echo $generator->generate($motdItemCollection); 
//output: A Minecraft Server
```

## Custom formatter

### Define new class formatter
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
```php
$formatCollection = new \DevLancer\MinecraftMotdParser\FormatCollection();
$formatCollection->add(new CustomBoldFormatter());

$motdItem = new \DevLancer\MinecraftMotdParser\MotdItem();
$motdItem->setBold(true);
$motdItem->setText("Hello World");

$motdItemCollection = new \DevLancer\MinecraftMotdParser\MotdItemCollection();
$motdItemCollection->add($motdItem);

$generator = new \DevLancer\MinecraftMotdParser\Generator\HtmlGenerator($formatCollection);
echo $generator->generate($motdItemCollection); 
```

### Output
```html
<b class="CustomBoldFormatter">Hello World</b>
```

## License

[MIT](LICENSE)