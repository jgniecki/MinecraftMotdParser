# Formatter

Minecraft MOTD uses special formatting codes (characters or arrays) to modify the appearance of text. Our library allows you to define custom formatters alongside standard codes, giving you control over text formatting.

It’s important to note that in the current version, the `TextParser` class uses regular expressions, limiting formatting to standard MOTD keys (`0-9`, `a-f`, `k-o`, `r`). This limitation does not apply to the `ArrayParser` class.

## Table of Contents
1. [Creating a Formatter](formetter.md#creating-a-formatter)
   1. [Basic Formatter](formetter.md#basic-formatter)
   2. [Generating HTML Code](formetter.md#generating-html-code)
   3. [Color Formatter](formetter.md#color-formatter)
   4. [Pre-built Color Formatter](formetter.md#pre-built-color-formatter)
2. [Formatter Collections](formetter.md#formatter-collections)
   1. [FormatCollection](formetter.md#formatcollection)
   2. [ColorCollection](formetter.md#colorcollection)
3. [List of Formatters](formetter.md#list-of-formatters)

## Creating a Formatter

### Basic Formatter
Each formatter must implement the `DevLancer\MinecraftMotdParser\Contracts\FormatterInterface` interface:
```php
class ObfuscatedFormat implements FormatterInterface
{
    public function getKey(): string
    {
        return 'k';
    }

    public function getName(): string
    {
        return 'obfuscated';
    }
    
    public function getFormat(): string
    {
        return '%s';
    }
}
```

- The `getKey()` method returns a single character that will be used as the formatter's key, such as `k`, `1`, `c`, corresponding to the MOTD key: `&k`, `&1`, `&c`. Each key must be unique.
- `getName()` returns the unique name of the formatter. This name should match the method name in the `MotdItem` class that returns its value. The method must be prefixed with `is` or `get` and written in `camelCase`, e.g., `isObfuscated()`. In the case of `ArrayParser`, this name will also be the key in the MOTD array:
    ```php
    'extra' => [
        'obfuscated' => true
    ]
    ```
- `getFormat()` returns the text formatting pattern used in the `sprintf` function.

### Generating HTML Code
To generate HTML code, the formatter should implement the `DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface`. Additional methods `getStyle()` and `getTag()` allow generating optimized HTML code:
```php
class BoldFormat implements HtmlFormatterInterface
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
        return '%s';
    }

    public function getStyle(): string
    {
        return 'font-weight: bold;';
    }

    public function getTag(): string
    {
        return 'span';
    }
}
```

- `getStyle()` returns attributes accepted by the `style` attribute in HTML.
- `getTag()` returns the HTML tag name within which the formatted text will be placed.

### Color Formatter
To format colors, implement the `DevLancer\MinecraftMotdParser\Contracts\ColorFormatterInterface`. Note that although `ColorFormatterInterface` extends `FormatterInterface`, it should not be added to `FormatCollection`, because the name returned by `getName()` must be unique to each formatter in that collection. It's also good practice to implement `HtmlFormatterInterface` for better HTML code generation:
```php
class ColorFormat implements ColorFormatterInterface
{
    public function getKey(): string
    {
        return '1';
    }

    public function getName(): string
    {
        return 'color';
    }

    public function getColorName(): string
    {
        return 'dark_blue';
    }

    public function getColor(): string
    {
        return '#0000AA';
    }
    
    public function getFormat(): string
    {
        return '<span style="color: ' . $this->getColor() . ';">%s</span>';
    }
}
```

- `getName()` must return `color`, as it is used as the key in the array:
    ```php
    'extra' => [
        'color' => 'dark_blue'
    ]
    ```
- `getColorName()` returns the color name, which is particularly important when using the `ArrayParser` class.
- `getColor()` returns the color in HEX format.
- `getFormat()` returns the formatting pattern with the color.

### Pre-built Color Formatter
Instead of creating a custom color formatter, you can use the pre-built `ColorFormat` class:
```php
use DevLancer\MinecraftMotdParser\Formatter\ColorFormat;

$redColor = new ColorFormat('c', 'red', '#FF5555');
```

## Formatter Collections
Each formatter should have a unique key (`key`) and name (`name`), and in the case of colors, a unique color name (`color name`).

### FormatCollection
`DevLancer\MinecraftMotdParser\Collection\FormatCollection` stores collections of formatters (excluding colors).

**Generating the Default Collection**
```php
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;

$collection = FormatCollection::generate();
```

You can freely manipulate the collection — adding, removing elements, and searching for them by `name` or `key`.

**Manipulating the Collection**

You can manipulate the collection in any way, i.e., adding or removing its elements. Each element can be searched by `name` or `key`.
```php
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;

$collection = FormatCollection::generate();
$bold = $collection->get('bold');
$l = $collection->get('l');
printr(($bold === $l)); //true
$collection->remove('l');
```

**Overriding a Formatter**

Let's say you want to override (or add) the format for `bold`. The easiest way is to extend the existing `BoldFormat` class and change what you need, for example, the value for the `style` attribute:

```php
class MyBoldFormat extends BoldFormat
{
    public function getStyle(): string
    {
        return 'font-weight: 900;';
    }
}
```

Now, add the formatter to the collection. Since the collection already contains a `bold` format, it will be overwritten.

```php
$collection = FormatCollection::generate();
$collection->get('bold')->getStyle(); //font-weight: bold;

$collection->add(new MyBoldFormat());
$collection->get('bold')->getStyle(); //font-weight: 900;
```

### ColorCollection
`DevLancer\MinecraftMotdParser\Collection\ColorCollection` stores collections of color formatters.

**Generating the Default Collection**
```php
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;

$collection = ColorCollection::generate();
```

**Adding New Colors**
```php
$lightGold = new ColorFormat('z', 'light_gold', '#FDDC5C');
$collection->add($lightGold);
```

---

## List of Formatters
Formatting codes aligned with the list on the [Minecraft Wiki](https://minecraft.fandom.com/wiki/Formatting_codes):

| Class               | Code | Name          |
|:--------------------|:-----|:--------------|
| ObfuscatedFormat     | k    | obfuscated    |
| BoldFormat           | l    | bold          |
| StrikethroughFormat  | m    | strikethrough |
| UnderlinedFormat     | n    | underline     |
| ItalicFormat         | o    | italic        |
| ResetFormat          | r    | reset         |

Color formatters are created using the `ColorFormat` class. Below is a list of available colors:

| Code | Name         |
|:-----|:-------------|
| 0    | black        |
| 1    | dark_blue    |
| 2    | dark_green   |
| 3    | dark_aqua    |
| 4    | dark_red     |
| 5    | dark_purple  |
| 6    | gold         |
| 7    | gray         |
| 8    | dark_gray    |
| 9    | blue         |
| a    | green        |
| b    | aqua         |
| c    | red          |
| d    | light_purple |
| e    | yellow       |
| f    | white        |

---