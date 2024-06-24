<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

use DevLancer\MinecraftMotdParser\MotdItemCollection;

interface ParserInterface
{
    /**
     * @param mixed $data
     * @param MotdItemCollection $collection
     * @return MotdItemCollection
     */
    public function parse($data, MotdItemCollection $collection): MotdItemCollection;

    /**
     * @param mixed $data
     * @return bool
     */
    public function supports($data): bool;
}