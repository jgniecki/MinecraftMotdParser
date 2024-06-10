<?php

namespace DevLancer\MinecraftMotdParser;

use ArrayIterator;
use DevLancer\MinecraftMotdParser\Contracts\ColorFormatterInterface;
use DevLancer\MinecraftMotdParser\Formatter\ColorFormat;

class ColorCollection  implements \Countable, \IteratorAggregate
{
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
     * @return array<string, ColorFormatterInterface>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     * @return ArrayIterator<string, ColorFormatterInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new \ArrayIterator($this->all());
    }

    /**
     * @param ColorFormatterInterface $item
     * @return void
     */
    public function add(ColorFormatterInterface $item): void
    {
        $this->items[$item->getKey()] = $item;
        $this->alias[$item->getName()] = $item->getKey();
    }

    /**
     * @param string $key
     * @return ColorFormatterInterface|null
     */
    public function get(string $key): ?ColorFormatterInterface
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
        $collection->add(new ColorFormat("0", "black", "#000000"));
        $collection->add(new ColorFormat("1", "dark_blue", "#0000AA"));
        $collection->add(new ColorFormat("2", "dark_green", "#00AA00"));
        $collection->add(new ColorFormat("3", "dark_aqua", "#00AAAA"));
        $collection->add(new ColorFormat("4", "dark_red", "#AA0000"));
        $collection->add(new ColorFormat("5", "dark_purple", "#AA00AA"));
        $collection->add(new ColorFormat("6", "gold", "#FFAA00"));
        $collection->add(new ColorFormat("7", "gray", "#AAAAAA"));
        $collection->add(new ColorFormat("8", "dark_gray", "#555555"));
        $collection->add(new ColorFormat("9", "blue", "#5555FF"));
        $collection->add(new ColorFormat("a", "green", "#55FF55"));
        $collection->add(new ColorFormat("b", "aqua", "#55FFFF"));
        $collection->add(new ColorFormat("c", "red", "#FF5555"));
        $collection->add(new ColorFormat("d", "light_purple", "#FF55FF"));
        $collection->add(new ColorFormat("e", "yellow", "#FFFF55"));
        $collection->add(new ColorFormat("f", "white", "#FFFFFF"));

        return $collection;
    }
}