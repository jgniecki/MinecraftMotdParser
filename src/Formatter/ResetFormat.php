<?php

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\Contracts\FormatterInterface;

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