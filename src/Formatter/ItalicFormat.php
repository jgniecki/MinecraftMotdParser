<?php

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface;

class ItalicFormat implements HtmlFormatterInterface
{
    use FormatTrait;
    public function getKey(): string
    {
        return 'o';
    }

    public function getName(): string
    {
        return 'italic';
    }

    public function getStyle(): string
    {
        return 'font-style: italic;';
    }

    public function getTag(): string
    {
        return 'span';
    }
}