<?php

namespace DevLancer\MinecraftMotdParser\Generator;

use DevLancer\MinecraftMotdParser\Contracts\GeneratorInterface;
use DevLancer\MinecraftMotdParser\MotdItemCollection;

class TextGenerator implements GeneratorInterface
{
    public function generate(MotdItemCollection $collection): string
    {
        $result = "";

        foreach ($collection as $motdItem) {
            if  ($motdItem->getText())
                $result .= $motdItem->getText();
        }

        return $result;
    }
}