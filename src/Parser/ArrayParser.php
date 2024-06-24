<?php

namespace DevLancer\MinecraftMotdParser\Parser;

use DevLancer\MinecraftMotdParser\ColorCollection;
use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;
use DevLancer\MinecraftMotdParser\Contracts\ParserInterface;
use DevLancer\MinecraftMotdParser\MotdItem;
use DevLancer\MinecraftMotdParser\MotdItemCollection;

class ArrayParser implements ParserInterface
{
    private ?MotdItemCollection $list = null;
    private ColorCollection  $colorCollection;
    public function __construct(?ColorCollection  $colorCollection = null)
    {
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
    }

    /**
     * @param array $data
     * @param MotdItemCollection $collection
     * @return MotdItemCollection
     */
    public function parse($data, MotdItemCollection $collection): MotdItemCollection
    {
        if (!$this->supports($data)) {
            throw new \InvalidArgumentException("Unsupported data");
        }

        if (\array_keys($data) !== \range(0, \count($data) - 1))
            $data = [$data];

        $this->list = $collection;
        $this->generate($data, new MotdItem());

        return $this->list;
    }

    private function generate(array $data, MotdItemInterface $parent): void
    {
        foreach ($data as $item) {
            if (empty($item))
                continue;

            $motdItem = clone $parent;

            if (\is_string($item)) {
                $motdItem->setText($item);
                $this->list->add($motdItem);
                continue;
            }

            if (isset($item['color'])) {
                if (strpos($item['color'], '#') === false)
                    $color = $this->colorCollection->get($item['color']);
                else
                    $color = $this->colorCollection->getByColor($item['color']);
                $color = (!$color)? $item['color'] : $color->getKey();
                $motdItem->setColor($color);
            }
            if (isset($item['bold']))
                $motdItem->setBold((bool) $item['bold']);

            if (isset($item['underlined']))
                $motdItem->setUnderlined((bool) $item['underlined']);

            if (isset($item['strikethrough']))
                $motdItem->setStrikethrough((bool) $item['strikethrough']);

            if (isset($item['italic']))
                $motdItem->setItalic((bool) $item['italic']);

            if (isset($item['obfuscated']))
                $motdItem->setObfuscated((bool) $item['obfuscated']);

            if (isset($item['reset']))
                $motdItem->setReset((bool) $item['reset']);

            if (isset($item['text'])) {
                $text = $item['text'];
                $newLine = \strpos($text, "\n");
                $motdItem->setText(($newLine === false)? $text : \substr($text, 0, $newLine));
                $this->list->add($motdItem);

                if ($newLine !== false) {
                    $newMotdItem = new MotdItem();
                    $newMotdItem->setText("\n");
                    $this->list->add($newMotdItem);

                    if (\strlen(\substr($text, $newLine+1)) > 0)
                        $this->generate([['text' => \substr($text, $newLine+1)]], $motdItem);
                }
            }

            if (isset($item['extra']))
                $this->generate($item['extra'], clone $motdItem);
        }
    }

    /**
     * @param array $data
     * @return bool
     */
    public function supports($data): bool
    {
        return \is_array($data) && !empty($data);
    }
}