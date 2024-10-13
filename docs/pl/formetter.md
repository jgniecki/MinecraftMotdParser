# Formatter

Minecraft MOTD wykorzystuje specjalne kody formatowania (znaki lub tablice) do modyfikacji wyglądu tekstu. Nasza biblioteka pozwala na definiowanie własnych formatterów obok standardowych kodów, dzięki czemu można kontrolować sposób formatowania tekstu.

## Spis treści
1. [Tworzenie formattera](formetter.md#tworzenie-formattera)
   1. [Podstawowy formatter](formetter.md#podstawowy-formatter)
   2. [Generowanie kodu HTML](formetter.md#generowanie-kodu-html)
   3. [Kolorowy formatter](formetter.md#kolorowy-formatter)
   4. [Gotowa implementacja kolorowego formattera](formetter.md#gotowa-implementacja-kolorowego-formattera)
2. [Kolekcje formatterów](formetter.md#kolekcje-formatterów)
   1. [FormatCollection](formetter.md#formatcollection)
   2. [ColorCollection](formetter.md#colorcollection)
3. [Lista formatterów](formetter.md#lista-formatterów)

## Tworzenie formattera

### Podstawowy formatter
Każdy formatter musi implementować interfejs `DevLancer\MinecraftMotdParser\Contracts\FormatterInterface`:
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

- Metoda `getKey()` zwraca pojedynczy znak, który będzie używany jako klucz formattera, np. `k`, `1`, `c`, co odpowiada kluczowi MOTD: `&k`, `&1`, `&c`. Każdy klucz musi być unikalny.
- `getName()` zwraca unikalną nazwę formattera. Nazwa ta powinna odpowiadać nazwie metody w klasie `MotdItem`, która zwraca jego wartość. Metoda musi być poprzedzona prefiksem `is` lub `get` i zapisana w formacie `camelCase` np. `isObfuscated()`. W przypadku `ArrayParser`, ta nazwa będzie również kluczem w tablicy MOTD:
    ```php
    'extra' => [
        'obfuscated' => true
    ]
    ```
- `getFormat()` zwraca wzorzec formatowania tekstu używany w funkcji `sprintf`.

### Generowanie kodu HTML
Aby generować kod HTML, formatter powinien implementować interfejs `DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface`. Dodatkowe metody `getStyle()` i `getTag()` pozwalają na generowanie bardziej zoptymalizowanego kodu HTML:
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

- `getStyle()` zwraca atrybuty akceptowane przez styl `style` w HTML.
- `getTag()` zwraca nazwę tagu HTML, wewnątrz którego zostanie umieszczony formatowany tekst.

### Kolorowy formatter
Aby sformatować kolor, implementujemy interfejs `DevLancer\MinecraftMotdParser\Contracts\ColorFormatterInterface`. Należy pamiętać, że chociaż `ColorFormatterInterface` dziedziczy po `FormatterInterface`, nie powinno się go dodawać do `FormatCollection`, ponieważ nazwa zwracana przez `getName()` musi być unikalna dla każdego formattera w tej kolekcji. Dobrą praktyką jest również zaimplementowanie `HtmlFormatterInterface` dla lepszego generowania kodu HTML:
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

- `getName()` musi zwracać wartość `color`, ponieważ jest używana jako klucz w tablicy:
    ```php
    'extra' => [
        'color' => 'dark_blue'
    ]
    ```
- `getColorName()` zwraca nazwę koloru, która jest szczególnie ważna w przypadku użycia klasy `ArrayParser`.
- `getColor()` zwraca kolor w formacie HEX.
- `getFormat()` zwraca wzorzec formatowania z kolorem.

### Gotowa implementacja kolorowego formattera
Zamiast tworzyć własny formatter dla koloru, możesz użyć gotowej klasy `ColorFormat`:
```php
use DevLancer\MinecraftMotdParser\Formatter\ColorFormat;

$redColor = new ColorFormat('c', 'red', '#FF5555');
```

## Kolekcje formatterów
Każdy formatter powinien mieć unikalny klucz (`key`) i nazwę (`name`), a w przypadku kolorów unikalną nazwę koloru (`color name`).

### FormatCollection
`DevLancer\MinecraftMotdParser\Collection\FormatCollection` przechowuje kolekcje formatterów (z wyłączeniem kolorów).

**Generowanie domyślnej kolekcji**
```php
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;

$collection = FormatCollection::generate();
```

Kolekcją można dowolnie manipulować — dodawać, usuwać elementy oraz wyszukiwać je po `name` lub `key`.

**Manipulowanie kolekcją**

Kolekcją można w dowolny sposób manipulować, tj. dodawać, usuwać jej elementy. Każdy element można wyszukać po `name` bądź po `key`.
```php
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;

$collection = FormatCollection::generate();
$bold = $collection->get('bold');
$l = $collection->get('l');
printr(($bold === $l)); //true
$collection->remove('l');
```

**Nadpisanie formattera**

Załóżmy, że zależy Ci aby nadpisać (bądź dodać) format dla `bold` najprostszym sposobem będzie dziedziczenie istniejącej klasy `BoldFormat`
i zmienienie tego, na czym Ci zależy, np. wartości dla atrybutu `style`:

```php
class MyBoldFormat extends BoldFormat
{
    public function getStyle(): string
    {
        return 'font-weight: 900;';
    }
}
```

Teraz należy dodać nasz formatter do kolekcji, z racji iż w naszej kolekcji po jej wygenerowaniu już istnieje format dla `bold`
zostanie on nadpisany

```php
$collection = FormatCollection::generate();
$collection->get('bold')->getStyle(); //font-weight: bold;

$collection->add(new MyBoldFormat());
$collection->get('bold')->getStyle(); //font-weight: 900;
```

### ColorCollection
`DevLancer\MinecraftMotdParser\Collection\ColorCollection` przechowuje kolekcje formatterów dla kolorów.

**Generowanie domyślnej kolekcji**
```php
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;

$collection = ColorCollection::generate();
```

**Dodawanie nowych kolorów**
```php
$lightGold = new ColorFormat('z', 'light_gold', '#FDDC5C');
$collection->add($lightGold);
```

---

## Lista formatterów
Zgodność kodów formatów z listą na stronie [minecraft wiki](https://minecraft.fandom.com/wiki/Formatting_codes):

| Class               | Code | Name          |
|:--------------------|:-----|:--------------|
| ObfuscatedFormat     | k    | obfuscated    |
| BoldFormat           | l    | bold          |
| StrikethroughFormat  | m    | strikethrough |
| UnderlinedFormat     | n    | underline     |
| ItalicFormat         | o    | italic        |
| ResetFormat          | r    | reset         |

Formattery dla kolorów tworzone są za pomocą klasy `ColorFormat`, oto lista dostępnych kolorów:

| Code               | Name               |
|:-------------------|:-------------------|
| 0                  | black              |
| 1                  | dark_blue          |
| 2                  | dark_green         |
| 3                  | dark_aqua          |
| 4                  | dark_red           |
| 5                  | dark_purple        |
| 6                  | gold               |
| 7                  | gray               |
| 8                  | dark_gray          |
| 9                  | blue               |
| a                  | green              |
| b                  | aqua               |
| c                  | red                |
| d                  | light_purple       |
| e                  | yellow             |
| f                  | white              |
| g                  | minecoin_gold      |
| h                  | material_quartz    |
| i                  | material_iron      |
| j                  | material_netherite |
| p                  | material_gold      |
| q                  | material_emerald   |
| s                  | material_diamond   |
| t                  | material_lapis     |
| u                  | material_amethyst  |
| v (changed from n) | material_copper    |
| x (changed from m) | material_redstone  |

---