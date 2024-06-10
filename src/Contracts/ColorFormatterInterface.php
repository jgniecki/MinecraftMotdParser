<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

interface ColorFormatterInterface extends FormatterInterface
{
    public function getColor(): string;
}