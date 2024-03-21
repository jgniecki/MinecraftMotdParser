<?php

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\FormatterInterface;

class ObfuscatedFormat implements FormatterInterface
{
    use FormatTrait;

    public function getKey(): string
    {
        return 'k';
    }

    public function getName(): string
    {
        return "obfuscated";
    }
}