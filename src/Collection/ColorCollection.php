<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Collection;

use ArrayIterator;
use Countable;
use DevLancer\MinecraftMotdParser\Contracts\ColorFormatterInterface;
use DevLancer\MinecraftMotdParser\Formatter\ColorFormat;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<string, ColorFormatterInterface>
 */
class ColorCollection implements Countable, IteratorAggregate
{
    /**
     * @var array<string, ColorFormatterInterface>
     */
    private array $items = [];

    /**
     * @var array<string, string>
     */
    private array $alias = [];

    public static function generate(): self
    {
        $collection = new self();
        $collection->add(new ColorFormat('0', 'black', '#000000'));
        $collection->add(new ColorFormat('1', 'dark_blue', '#0000AA'));
        $collection->add(new ColorFormat('2', 'dark_green', '#00AA00'));
        $collection->add(new ColorFormat('3', 'dark_aqua', '#00AAAA'));
        $collection->add(new ColorFormat('4', 'dark_red', '#AA0000'));
        $collection->add(new ColorFormat('5', 'dark_purple', '#AA00AA'));
        $collection->add(new ColorFormat('6', 'gold', '#FFAA00'));
        $collection->add(new ColorFormat('7', 'gray', '#AAAAAA'));
        $collection->add(new ColorFormat('8', 'dark_gray', '#555555'));
        $collection->add(new ColorFormat('9', 'blue', '#5555FF'));
        $collection->add(new ColorFormat('a', 'green', '#55FF55'));
        $collection->add(new ColorFormat('b', 'aqua', '#55FFFF'));
        $collection->add(new ColorFormat('c', 'red', '#FF5555'));
        $collection->add(new ColorFormat('d', 'light_purple', '#FF55FF'));
        $collection->add(new ColorFormat('e', 'yellow', '#FFFF55'));
        $collection->add(new ColorFormat('f', 'white', '#FFFFFF'));

        return $collection;
    }

    public function add(ColorFormatterInterface $item): void
    {
        $this->items[$item->getKey()] = $item;
        $this->alias[$item->getColorName()] = $item->getKey();
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return ArrayIterator<string, ColorFormatterInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->all());
    }

    /**
     * @return array<string, ColorFormatterInterface>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function remove(string $key): void
    {
        $item = $this->get($key);

        if ($item) {
            unset($this->items[$item->getKey()], $this->alias[$item->getColorName()]);
        }
    }

    /**
     * Retrieves a ColorFormatterInterface instance based on the provided key.
     *
     * This method attempts to retrieve a color formatter using the following steps:
     * 1. If the provided key exists in the `$items` array, the corresponding `ColorFormatterInterface` is returned.
     * 2. If the key is an alias found in the `$alias` array, the actual key is retrieved and the process continues.
     * 3. If the key resembles a color (contains `#`) and matches a color formatter via the `getByColor()` method, the first formatter
     *    that matches the provided HEX color is returned.
     * 4. If none of the above conditions are met, the method returns the value associated with the key in the `$items` array or `null` if not found.
     *
     * @param string $key The key or HEX color used to retrieve the color formatter. The key can either be:
     * - A direct key in the `$items` array.
     * - An alias that is mapped to a key in the `$items` array.
     * - A HEX color code (e.g., "#FFFFFF").
     * @return ColorFormatterInterface|null Returns the matching `ColorFormatterInterface` instance or `null` if no match is found.
     */
    public function get(string $key): ?ColorFormatterInterface
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        if (isset($this->alias[$key])) {
            $key = $this->alias[$key];
        }

        if (strpos($key, "#") && $this->getByColor($key)) {
            return $this->getByColor($key);
        }

        return $this->items[$key] ?? null;
    }

    /**
     * Retrieves the first ColorFormatterInterface instance that uses the specified color.
     * If multiple formatters use the given color, the first matching instance is returned.
     *
     * Iterates over the collection of color formatters and compares the specified HEX color
     * with the result of each formatter's getColor() method.
     *
     * @param string $color HEX color code to search for (e.g., "#FFFFFF").
     * @return ColorFormatterInterface|null Returns the first matching ColorFormatterInterface
     * instance, or null if no formatter with the specified color is found.
     */
    public function getByColor(string $color): ?ColorFormatterInterface
    {
        foreach ($this->items as $item) {
            if ($item->getColor() === $color) {
                return $item;
            }
        }

        return null;
    }
}
