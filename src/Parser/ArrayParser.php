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
    public function __construct(ColorCollection  $colorCollection)
    {
        $this->colorCollection = $colorCollection;
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

        $this->list = $collection;
        $this->generate([$data], new MotdItem());

        return $this->list;
    }

    private function generate(array $list, MotdItemInterface $parent): void
    {
        foreach ($list as $data) {
            $container = clone $parent;

            if (is_string($data)) {
                $container->setText($data);
                $this->list->add($container);
                continue;
            }

            if (isset($data['color'])) {
                $color = $this->colorCollection->get($data['color']);
                if (!$color)
                    $color = $data['color'];
                $container->setColor($color);
            }

            if (isset($data['bold']))
                $container->setBold((bool) $data['bold']);

            if (isset($data['underlined']))
                $container->setUnderlined((bool) $data['underlined']);

            if (isset($data['strikethrough']))
                $container->setStrikethrough((bool) $data['strikethrough']);

            if (isset($data['italic']))
                $container->setItalic((bool) $data['italic']);

            if (isset($data['text'])) {
                $text = $data['text'];
                $newLine = strpos($text, "\n");
                $container->setText(($newLine === false)? $text : substr($text, 0, $newLine));
            }

            $this->list->add($container);
            if (isset($newLine) && $newLine !== false && isset($text)) {
                $container = new MotdItem();
                $container->setText("\n");
                $this->list->add($container);

                if (strlen(substr($text, $newLine+1)) > 0)
                    $this->generate([['text' => substr($text, $newLine+1)]], new MotdItem());

                $container = new MotdItem();
            }

            if (isset($data['extra']))
                $this->generate($data['extra'], clone $container);
        }
    }

    public function supports($data): bool
    {
        return \is_array($data);
    }
}