<?php

declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Parser;

use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;
use DevLancer\MinecraftMotdParser\Contracts\ParserInterface;
use DevLancer\MinecraftMotdParser\EntryValueMotdItemTrait;
use DevLancer\MinecraftMotdParser\MotdItem;
use InvalidArgumentException;

class ArrayParser implements ParserInterface
{
    use EntryValueMotdItemTrait;

    private ?MotdItemCollection $list = null;
    private ColorCollection $colorCollection;
    private FormatCollection $formatCollection;

    public function __construct(?FormatCollection $formatCollection = null, ?ColorCollection $colorCollection = null)
    {
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
        $this->formatCollection = $formatCollection ?? FormatCollection::generate();
    }

    private function generate(array $motd, MotdItemInterface $parent): void
    {
        foreach ($motd as $partMotd) {
            if (empty($partMotd)) {
                continue;
            }

            $motdItem = clone $parent;

            if (is_string($partMotd)) {
                $motdItem->setText($partMotd);
                $this->list->add($motdItem);

                continue;
            }

            if (isset($partMotd['color'])) {
                $color = $this->colorCollection->get($partMotd['color']);
                $color = ($color)? $color->getKey() : $partMotd['color'];

                $motdItem->setColor($color);
            }

            foreach ($this->formatCollection->all() as $formatter) {
                $name = $formatter->getName();
                if (!isset($partMotd[$name])) {
                    continue;
                }

                $this->entryValueMotdItem($name, (bool)$partMotd[$name], $motdItem);
            }

            if (isset($partMotd['text'])) {
                $text = $partMotd['text'];
                $newLine = strpos($text, "\n");
                $motdItem->setText((false === $newLine) ? $text : substr($text, 0, $newLine));
                $this->list->add($motdItem);

                if (false !== $newLine) {
                    $newMotdItem = new MotdItem();
                    $newMotdItem->setText("\n");
                    $this->list->add($newMotdItem);

                    if (strlen(substr($text, $newLine + 1)) > 0) {
                        $this->generate([['text' => substr($text, $newLine + 1)]], $motdItem);
                    }
                }
            }

            if (isset($partMotd['extra'])) {
                $this->generate($partMotd['extra'], clone $motdItem);
            }
        }
    }

    /**
     * @param array $data
     */
    public function parse($data, MotdItemCollection $collection): MotdItemCollection
    {
        if (!$this->supports($data)) {
            throw new InvalidArgumentException('Unsupported data');
        }

        if (array_keys($data) !== range(0, count($data) - 1)) {
            $data = [$data];
        }

        $this->list = $collection;
        $this->generate($data, new MotdItem());

        return $this->list;
    }

    /**
     * @param array $data
     */
    public function supports($data): bool
    {
        return is_array($data) && !empty($data);
    }
}
