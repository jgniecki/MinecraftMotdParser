<?php

namespace DevLancer\MinecraftMotdParser;

use ArrayIterator;
use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;

class MotdItemCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var MotdItemInterface[]
     */
    private array $items = [];

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * @return array<int, MotdItemInterface>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     * @return ArrayIterator<int, MotdItemInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new \ArrayIterator($this->all());
    }

    /**
     * @param MotdItemInterface $item
     * @return void
     */
    public function add(MotdItemInterface $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @param int $id
     * @return MotdItemInterface|null
     */
    public function get(int $id): ?MotdItemInterface
    {
        return $this->items[$id] ?? null;
    }

    /**
     * @param int $id
     * @return void
     */
    public function remove(int $id): void
    {
        unset($this->items[$id]);
        $this->items = array_values($this->items);
    }

    public function mergeSimilarItem()
    {
        do {
            $old = clone $this;
            for ($i = 1; $i < $this->count(); $i++) {
                $left = $this->get($i-1);
                $right = $this->get($i);
                if ($this->compareItem($left, $right)) {
                    $this->get($i-1)->setText($left->getText() . $right->getText());
                    $this->remove($i);
                    $i++;
                }
            }
        } while($old != $this);
    }

    public function compareItem(MotdItemInterface $motdItemLeft, MotdItemInterface $motdItemRight): bool
    {
        $motdItemLeft = clone $motdItemLeft;
        $motdItemRight = clone $motdItemRight;

        $motdItemLeft->setText(null);
        $motdItemRight->setText(null);

        return $motdItemLeft == $motdItemRight;
    }
}