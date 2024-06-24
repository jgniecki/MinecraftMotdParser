<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

interface MotdItemInterface
{
    public function getText(): ?string;
    public function setText(string $text): void;
    public function getColor(): ?string;
    public function setColor(string $color): void;
    public function isObfuscated(): bool;
    public function setObfuscated(bool $obfuscated): void;
    public function isBold(): bool;
    public function setBold(bool $bold): void;
    public function isUnderlined(): bool;
    public function setUnderlined(bool $underlined): void;
    public function isStrikethrough(): bool;
    public function setStrikethrough(bool $strikethrough): void;
    public function isItalic(): bool;
    public function setItalic(bool $italic): void;
    public function isReset(): bool;
    public function setReset(bool $reset): void;

}