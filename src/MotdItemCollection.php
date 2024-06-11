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
        $this->items = \array_values($this->items);
    }

    /**
     * Merges adjacent items in the collection that are considered similar.
     *
     * This method iterates through the collection, comparing each pair of adjacent items using the compareItem method.
     * If two adjacent items are considered similar, their text properties are concatenated, and the second item is removed.
     * The process repeats until no further merges are possible.
     *
     * Note: Items with the text value of "\n" (newline) are not merged.
     *
     * @return void
     */
    public function mergeSimilarItem()
    {
        do {
            $old = clone $this;
            for ($i = 1; $i < $this->count(); $i++) {
                $left = $this->get($i-1);
                $right = $this->get($i);
                if ($this->compareItem($left, $right) && $left->getText() != "\n" && $right->getText() != "\n") {
                    $this->get($i-1)->setText($left->getText() . $right->getText());
                    $this->remove($i);
                    $i++;
                }
            }
        } while($old != $this);
    }

    /**
     * Compares two MotdItemInterface objects for equality, ignoring the text property.
     *
     * This method creates clones of the provided objects to avoid modifying the originals.
     * It then sets the text property of both cloned objects to null before comparing them.
     * The comparison checks if all other properties of the two objects are equal.
     *
     * @param MotdItemInterface $motdItemLeft The first MotdItemInterface object to compare.
     * @param MotdItemInterface $motdItemRight The second MotdItemInterface object to compare.
     * @return bool Returns true if the modified cloned objects are considered equal, false otherwise.
     */
    public function compareItem(MotdItemInterface $motdItemLeft, MotdItemInterface $motdItemRight): bool
    {
        $motdItemLeft = clone $motdItemLeft;
        $motdItemRight = clone $motdItemRight;

        $motdItemLeft->setText(null);
        $motdItemRight->setText(null);

        return $motdItemLeft == $motdItemRight;
    }
}