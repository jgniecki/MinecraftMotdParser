<?php

namespace DevLancer\MinecraftMotdParser;


use ArrayIterator;
use DevLancer\MinecraftMotdParser\Contracts\FormatterInterface;
use DevLancer\MinecraftMotdParser\Formatter\BoldFormat;
use DevLancer\MinecraftMotdParser\Formatter\ItalicFormat;
use DevLancer\MinecraftMotdParser\Formatter\ObfuscatedFormat;
use DevLancer\MinecraftMotdParser\Formatter\ResetFormat;
use DevLancer\MinecraftMotdParser\Formatter\StrikethroughFormat;
use DevLancer\MinecraftMotdParser\Formatter\UnderlinedFormat;

class FormatCollection  implements \Countable, \IteratorAggregate
{
    /**
     * @var FormatterInterface[] $items;
     */
    private array $items = [];
    private array $alias = [];

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * @return array<string, FormatterInterface>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     * @return ArrayIterator<string, FormatterInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new \ArrayIterator($this->all());
    }

    /**
     * @param FormatterInterface $item
     * @return void
     */
    public function add(FormatterInterface $item): void
    {
        $this->items[$item->getKey()] = $item;
        $this->alias[$item->getName()] = $item->getKey();
    }

    /**
     * @param string $key
     * @return FormatterInterface|null
     */
    public function get(string $key): ?FormatterInterface
    {
        if (isset($this->items[$key]))
            return $this->items[$key];

        if (isset($this->alias[$key]))
            $key = $this->alias[$key];

        return $this->items[$key] ?? null;
    }

    /**
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        $item = $this->get($key);

        if ($item) {
            unset($this->items[$item->getKey()]);
            unset($this->alias[$item->getName()]);
        }
    }

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
}