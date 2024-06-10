<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

interface FormatterInterface
{
    public function getKey(): string;
    public function getName(): string;
    public function getFormat(): string;
}