<?php

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\HtmlFormatterInterface;

class BoldFormat implements HtmlFormatterInterface
{
    use FormatTrait;

    public function getKey(): string
    {
        return 'l';
    }

    public function getName(): string
    {
        return 'bold';
    }

    public function getStyle(): string
    {
        return 'font-weight: bold;';
    }

    public function getTag(): string
    {
        return 'span';
    }
}