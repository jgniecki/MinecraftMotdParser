# Formatter
Minecraft MOTD ma zdefiniowane klucze (znaki) służące do formatowania tekstu bądź tablice.
Bibloteka za pomocą formatterów umożliwia definowanie właśnie takich kluczy, 
oprócz standardowych można zdefinować własne oraz ustawić sposób formatowania tekstu. 
Jednak należy pamiętać, że w obecnej wersji klasa `TextParser` używa wyrażenia regularnego
które w zakresie ma standardowe klucze MOTD (`0-9a-fklmnor`), dlatego należy to uwzględnić.
Klasa `ArrayParser` jest wolna od tego ograniczenia.

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
Każdy formatter musi implementować interfejs 
`DevLancer\MinecraftMotdParser\Contracts\FormatterInterface`
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

Metoda `getKey()` musi zwracać pojedynczy znak który będzie służył jako klucz formattera, np. k, 1, c i odpowiada to kluczowi MOTD czyli: &k, &1, &c. Należy pamiętać, że każdy klucz powinien być unikalny.

Metoda `getName()` zwraca nazwe formattera która musi być unikalna. Ta wartość może wskazywać na dwa miejsca.
Pierwszym istotnym przypadkiem na który musi wskazywać to nazwa metody w klasie `MotdItem` (bądź w klasie dziedziczącej) która będzie zwracała jej wartość.
Metoda musi być poprzedzona prefiksem `is` lub `get` a sam jej zapis musi zachowywać `camelCase`, np. `isObfuscated()`.
Drugim przypadkiem jest to klucz elementu w tablicy MOTD który jest wymagany wyłącznie dla klasy `ArrayParser`, czyli
```php
//...
    'extra' => [
        'obfuscated' => true
    ]
//...
```

Metoda `getFormat()` zwraca pattern w jaki sposób ma być sformatowany tekst z użyciem funkcji `sprintf`. 

### Generowanie kodu HTML
Używając klasy `HtmlGenerator` w celu wygenerowania kodu HTML można posłużyć się
formatterem który implementuje interfejs
`DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface`
w tym przypadku do standardowego formattera należy dopisać dwie metody `getStyle()` i `getTag()`.
Formatter implementujący ten interfejs umożliwia generatorowi wygenerować krótszy kod HTML poprzez grupowanie podobnych elementów na podstawie tagów HTML
i osadzanie w nich dla atrybutu `style` wartości zaczerpnięte z metody `getStyle()`.
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

Metoda `getStyle()` musi wzrócić parametr akceptowalny dla atrybutu `style` w HTMLu,
należy pamiętać o `;` który zakończy tą wartość.

Metoda `getTag()` musi zwrócić tag HTML w którym znajdzie się formatowany tekst.

### Kolorowy formatter
W przypadku gdy chcesz sformatować kolor należy zaimplementować interfejs
`DevLancer\MinecraftMotdParser\Contracts\ColorFormatterInterface`

Należy pamiętać, że mimo iż `ColorFormatterInterface` implementuje `FormatterInterface`, nie należy
dodawać go jako element do listy w obiekcie `FormatCollection`, ponieważ metoda `getName()` powinna zwracać wartość `color`
gdzie w przypadku `FormatCollection` każdy formatter powinien mieć tą wartość unikalną. Dodatkowo dobrą praktyką jest także,
zaimplementowanie `HtmlFormatterInterface` w celu lepszego formatowania kodu.
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

Metoda `getName()`, jak już wcześniej było wspominane wskazuje na klucz w tablicy dlatego powinna zwracać wartość `color`.
```php
//...
    'extra' => [
        'color' => 'dark_blue'
    ]
//...
```

Metoda `getColorName()` zwraca nazwę koloru, wartość ta jest szczególnie ważna w przypadku użycia klasy `ArrayParser` gdzie element o kluczu `color` w tablicy wskazuje właśnie tą wartość. 
Przykład jest podany powyżej.

Metoda `getColor()` musi zwracać kolor w zapisie HEX.

Metoda `getFormat()` zwraca pattern w jaki sposób będzie formatowany tekst

---

### Gotowa implementacja kolorowego formattera
Zamiast tworzyć nowego formattera dla koloru, warto użyć już gotowej klasy `ColorFormat`.

```php
use DevLancer\MinecraftMotdParser\Formatter\ColorFormat;

$redColor = new ColorFormat(
    'c',
    'red',
    '#FF5555'
);
```

---

## Kolekcje formatterów
Należy pamiętać, że każdy formatter powinien mieć unikalny `key` i `name` (w przypadku kolorów `color name`).
Wyszukując element w kolekcji za pomocą metody `get()` można robić to za pomocą `key` i `name` a dla kolorów `color name`.
Kolekcje nie posiadają powtórzeń, gdy istnieje już element od danym kluczu i zostanie dodany nowy o identycznym kluczu istniejący zostanie nadpisany.


### FormatCollection
Obiekt `DevLancer\MinecraftMotdParser\Collection\FormatCollection` przechowuje kolekcje formatterów (nie uwzględniając kolorów).

**Generowanie domyślnej kolekcji**

Klasa `FormatCollection` posiada metode która pozwala wygenerować kolekcje z domyślnymi formatterami ([lista poniżej](formetter.md#lista-formatterów)).
```php
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;

$collection = FormatCollection::generate();
```

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
$collection->get('bold')->getStyle(); //font-weight: 900;+
```

### ColorCollection
Obiekt `DevLancer\MinecraftMotdParser\Collection\ColorCollection` przechowuje kolekcje formatterów dla kolorów.
Należy pamiętać, że każdy formatter powinien mieć unikalny `key` i `color name`.

**Generowanie domyślnej kolekcji**

Klasa `ColorCollection` posiada metode która pozwala wygenerować kolekcje z domyślnymi formatterami dla kolorów ([lista poniżej](formetter.md#lista-formatterów)).
```php
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;

$collection = FormatCollection::generate();
```

Kolekcją można w dowolny sposób manipulować, tj. dodawać, usuwać jej elementy. Każdy element można wyszukać po nazwie koloru bądź po kluczu formattera.
```php
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;

$collection = ColorCollection::generate();
$red = $collection->get('red');
$c = $collection->get('c');
printr(($red === $c)); //true
$collection->remove('red');
```

**Dodawanie nowych kolorów**

Najprostszym rozwiązaniem będzie użyć klasy `ColorFormat` która powstała właśnie w tym celu.
```php
use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use \DevLancer\MinecraftMotdParser\Formatter\ColorFormat;

$collection = ColorCollection::generate();
$light_gold = new ColorFormat('z', 'light_gold', '#FDDC5C');
$collection->add($light_gold);
```
Gotowe, do kolekcji dodano nowy kolor, `key`: `z` i `color name`: `light_gold`

---
## Lista formatterów
Kody formatów oraz ich nazwy są zgodne z podaną listą na stronie [minecraft wiki](https://minecraft.fandom.com/wiki/Formatting_codes)

| Class               | Code | Name          |
|:--------------------|:-----|:--------------|
| ObfuscatedFormat    | k    | obfuscated    |
| BoldFormat          | l    | bold          |
| StrikethroughFormat | m    | strikethrough |
| UnderlinedFormat    | n    | underline     |
| ItalicFormat        | o    | italic        |
| ResetFormat         | r    | reset         |

Formattery dla kolorów są tworzone za pomocą klasy `ColorFormat`, lista kolorów:

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




