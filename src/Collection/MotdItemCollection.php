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
use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, MotdItemInterface>
 */
class MotdItemCollection implements Countable, IteratorAggregate
{
    /**
     * @var MotdItemInterface[]
     */
    private array $items = [];

    /**
     * @return ArrayIterator<int, MotdItemInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->all());
    }

    /**
     * @return array<int, MotdItemInterface>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function add(MotdItemInterface $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Merges adjacent items in the collection that are considered similar.
     *
     * This method iterates through the collection, comparing each pair of adjacent items using the compareItem method.
     * If two adjacent items are considered similar, their text properties are concatenated, and the second item is removed.
     * The process repeats until no further merges are possible.
     *
     * Note: Items with the text value of "\n" (newline) are not merged.
     */
    public function mergeSimilarItem()
    {
        do {
            $old = clone $this;
            for ($i = 1; $i < $this->count(); ++$i) {
                $left = $this->get($i - 1);
                $right = $this->get($i);
                if ($left && $right && $this->compareItem($left, $right) && "\n" != $left->getText() && "\n" != $right->getText()) {
                    $left->setText($left->getText() . $right->getText());
                    $this->remove($i);
                    ++$i;
                }
            }
        } while ($old != $this);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function get(int $id): ?MotdItemInterface
    {
        return $this->items[$id] ?? null;
    }

    /**
     * Compares two MotdItemInterface objects for equality, ignoring the text property.
     *
     * This method creates clones of the provided objects to avoid modifying the originals.
     * It then sets the text property of both cloned objects to null before comparing them.
     * The comparison checks if all other properties of the two objects are equal.
     *
     * @param MotdItemInterface $motdItemLeft the first MotdItemInterface object to compare
     * @param MotdItemInterface $motdItemRight the second MotdItemInterface object to compare
     *
     * @return bool returns true if the modified cloned objects are considered equal, false otherwise
     */
    public function compareItem(MotdItemInterface $motdItemLeft, MotdItemInterface $motdItemRight): bool
    {
        $motdItemLeft = clone $motdItemLeft;
        $motdItemRight = clone $motdItemRight;

        $motdItemLeft->setText('');
        $motdItemRight->setText('');

        return $motdItemLeft == $motdItemRight;
    }

    public function remove(int $id): void
    {
        unset($this->items[$id]);
        $this->items = array_values($this->items);
    }
}
