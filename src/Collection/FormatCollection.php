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
use DevLancer\MinecraftMotdParser\Contracts\FormatterInterface;
use DevLancer\MinecraftMotdParser\Formatter\BoldFormat;
use DevLancer\MinecraftMotdParser\Formatter\ItalicFormat;
use DevLancer\MinecraftMotdParser\Formatter\ObfuscatedFormat;
use DevLancer\MinecraftMotdParser\Formatter\ResetFormat;
use DevLancer\MinecraftMotdParser\Formatter\StrikethroughFormat;
use DevLancer\MinecraftMotdParser\Formatter\UnderlinedFormat;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<string, FormatterInterface>
 */
class FormatCollection implements Countable, IteratorAggregate
{
    /**
     * @var array<string, FormatterInterface>
     */
    private array $items = [];

    /**
     * @var array<string, string>
     */
    private array $alias = [];

    public static function generate(): self
    {
        $collection = new self();
        $collection->add(new BoldFormat());
        $collection->add(new ItalicFormat());
        $collection->add(new ObfuscatedFormat());
        $collection->add(new StrikethroughFormat());
        $collection->add(new UnderlinedFormat());
        $collection->add(new ResetFormat());

        return $collection;
    }

    public function add(FormatterInterface $item): void
    {
        $this->items[$item->getKey()] = $item;
        $this->alias[$item->getName()] = $item->getKey();
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return ArrayIterator<string, FormatterInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->all());
    }

    /**
     * @return array<string, FormatterInterface>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function remove(string $key): void
    {
        $item = $this->get($key);

        if ($item) {
            unset($this->items[$item->getKey()], $this->alias[$item->getName()]);
        }
    }

    public function get(string $key): ?FormatterInterface
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        if (isset($this->alias[$key])) {
            $key = $this->alias[$key];
        }

        return $this->items[$key] ?? null;
    }
}
