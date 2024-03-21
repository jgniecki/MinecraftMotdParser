<?php

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\FormatterInterface;

class ResetFormat implements FormatterInterface
{
    use FormatTrait;
    public function getKey(): string
    {
        return 'r';
    }

    public function getName(): string
    {
        return 'reset';
    }
}