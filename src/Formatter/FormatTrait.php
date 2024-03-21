<?php

namespace DevLancer\MinecraftMotdParser\Formatter;

trait FormatTrait
{
    private string $format = "%s";

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

}