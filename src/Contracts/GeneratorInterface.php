<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

use DevLancer\MinecraftMotdParser\MotdItemCollection;

interface GeneratorInterface
{
    public function generate(MotdItemCollection $collection): string;
}