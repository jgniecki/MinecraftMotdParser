<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

interface HtmlFormatterInterface extends FormatterInterface
{
    /**
     * Get css style used to format the tag (eq. 'color: #ffffff;')
     *
     * @return string
     */
    public function getStyle(): string;

    /**
     * Get html tag in which the format will be generated (eq. span)
     *
     * @return string
     */
    public function getTag(): string;
}