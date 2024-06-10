<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

interface HtmlFormatterInterface extends FormatterInterface
{
    public function getStyle(): string;

    public function getTag(): string;
}