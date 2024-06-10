<?php

namespace DevLancer\MinecraftMotdParser\Generator;

use DevLancer\MinecraftMotdParser\Contracts\GeneratorInterface;
use DevLancer\MinecraftMotdParser\MotdItemCollection;

class RawGenerator implements GeneratorInterface
{
    private string $symbol;
    public function __construct(string $symbol = "§")
    {
        $this->symbol = $symbol;
    }

    public function generate(MotdItemCollection $collection): string
    {
        $result = "";

        foreach ($collection as $motdItem) {
            $_item = "";

            if ($motdItem->isObfuscated())
                $_item .= $this->symbol . 'k';
            if ($motdItem->isBold())
                $_item .= $this->symbol . 'l';
            if ($motdItem->isStrikethrough())
                $_item .= $this->symbol . 'm';
            if ($motdItem->isUnderlined())
                $_item .= $this->symbol . 'n';
            if ($motdItem->isItalic())
                $_item .= $this->symbol . 'o';

            if ($motdItem->getColor()) {
                $color = $motdItem->getColor();
                if (strpos($color, '#') === false)
                    $_item .= $this->symbol . $motdItem->getColor();
            }

            if  ($motdItem->getText())
                $_item .= $motdItem->getText();

            if ($motdItem->isReset())
                $_item .= $this->symbol . 'r';
            $result .= $_item;
        }

        return $result;
    }
}