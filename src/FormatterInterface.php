<?php

namespace DevLancer\MinecraftMotdParser;

interface FormatterInterface
{
    public function getKey(): string;
    public function getName(): string;
    public function getFormat(): string;
}