<?php

namespace DevLancer\MinecraftMotdParser;

interface ColorFormatterInterface extends FormatterInterface
{
    public function getColor(): string;
}