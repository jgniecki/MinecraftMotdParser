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

    public function get(string $key): ?ColorFormatterInterface
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        if (isset($this->alias[$key])) {
            $key = $this->alias[$key];
        }

        return $this->items[$key] ?? null;
    }

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
