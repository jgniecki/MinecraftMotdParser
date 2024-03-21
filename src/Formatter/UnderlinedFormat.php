<?php

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\HtmlFormatterInterface;

class UnderlinedFormat implements HtmlFormatterInterface
{
    use FormatTrait;

    public function getKey(): string
    {
        return 'n';
    }

    public function getName(): string
    {
        return 'underlined';
    }

    public function getStyle(): string
    {
        return 'text-decoration: underline;';
    }

    public function getTag(): string
    {
        return 'span';
    }
}