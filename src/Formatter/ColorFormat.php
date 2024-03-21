<?php

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\ColorFormatterInterface;
use DevLancer\MinecraftMotdParser\HtmlFormatterInterface;

class ColorFormat implements HtmlFormatterInterface, ColorFormatterInterface
{
    use FormatTrait;
    private string $key;
    private string $name;
    private string $color;

    public function __construct(string $key, string $name, string $color)
    {
        $this->key = $key;
        $this->name = $name;
        $this->color = $color;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStyle(): string
    {
        return 'color: ' . $this->getColor();
    }

    public function getTag(): string
    {
        return 'span';
    }

    public function getColor(): string
    {
        return $this->color;
    }
}