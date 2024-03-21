<?php

namespace DevLancer\MinecraftMotdParser;

use DevLancer\MinecraftMotdParser\FormatterInterface;

interface HtmlFormatterInterface extends FormatterInterface
{
    public function getStyle(): string;

    public function getTag(): string;
}