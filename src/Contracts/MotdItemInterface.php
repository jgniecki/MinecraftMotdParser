<?php

namespace DevLancer\MinecraftMotdParser\Contracts;

interface MotdItemInterface
{
    public function getText(): ?string;
    public function setText(string $text);
    public function getColor(): ?string;
    public function setColor(string $color);
    public function isObfuscated(): bool;
    public function setObfuscated(bool $obfuscated);
    public function isBold(): bool;
    public function setBold(bool $bold);
    public function isUnderlined(): bool;
    public function setUnderlined(bool $underlined);
    public function isStrikethrough(): bool;
    public function setStrikethrough(bool $strikethrough);
    public function isItalic(): bool;
    public function setItalic(bool $italic);
    public function isReset(): bool;
    public function setReset(bool $reset);

}