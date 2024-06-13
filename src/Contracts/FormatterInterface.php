<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

/**
 * Interface FormatterInterface
 *
 * @package DevLancer\MinecraftMotdParser\Contracts
 */
interface FormatterInterface
{
    /**
     * Get the key of the formatter (eg. k, 1, c).
     *
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Get the name of the formatter (eg. bold, underlined, color, italic, reset, obfuscated, strikethrough).
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the format pattern used in the sprintf function.
     *
     * @return string
     */
    public function getFormat(): string;
}