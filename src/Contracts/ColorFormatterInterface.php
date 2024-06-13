<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

interface ColorFormatterInterface extends FormatterInterface
{
    /**
     * Get the hex color.
     *
     *
     * @return string
     */
    public function getColor(): string;

    /**
     * Get the color name of the formatter (eg. black, aqua, dark_red).
     *
     * @return string
     */
    public function getColorName(): string;
}