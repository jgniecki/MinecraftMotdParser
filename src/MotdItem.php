<?php

namespace DevLancer\MinecraftMotdParser;

use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;

class MotdItem implements \JsonSerializable, MotdItemInterface
{
    private ?string $text = null;
    private ?string $color = null;
    private bool $obfuscated = false;
    private bool $bold = false;
    private bool $strikethrough = false;
    private bool $underlined = false;
    private bool $italic = false;
    private bool $reset = false;

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return bool
     */
    public function isObfuscated(): bool
    {
        return $this->obfuscated;
    }

    /**
     * @param bool $obfuscated
     */
    public function setObfuscated(bool $obfuscated): void
    {
        $this->obfuscated = $obfuscated;
    }

    /**
     * @return bool
     */
    public function isBold(): bool
    {
        return $this->bold;
    }

    /**
     * @param bool $bold
     */
    public function setBold(bool $bold): void
    {
        $this->bold = $bold;
    }

    /**
     * @return bool
     */
    public function isUnderlined(): bool
    {
        return $this->underlined;
    }

    /**
     * @param bool $underlined
     */
    public function setUnderlined(bool $underlined): void
    {
        $this->underlined = $underlined;
    }

    /**
     * @return bool
     */
    public function isStrikethrough(): bool
    {
        return $this->strikethrough;
    }

    /**
     * @param bool $strikethrough
     */
    public function setStrikethrough(bool $strikethrough): void
    {
        $this->strikethrough = $strikethrough;
    }

    /**
     * @return bool
     */
    public function isItalic(): bool
    {
        return $this->italic;
    }

    /**
     * @param bool $italic
     */
    public function setItalic(bool $italic): void
    {
        $this->italic = $italic;
    }

    /**
     * @return bool
     */
    public function isReset(): bool
    {
        return $this->reset;
    }

    /**
     * @param bool $reset
     */
    public function setReset(bool $reset): void
    {
        $this->reset = $reset;
    }

    /**
     * @return array<string, string|null|bool>
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->getText(),
            'color' => $this->getColor(),
            'bold' => $this->isBold(),
            'italic' => $this->isItalic(),
            'obfuscated' => $this->isObfuscated(),
            'underlined' => $this->isUnderlined(),
            'strikethrough' => $this->isStrikethrough(),
            'reset' => $this->isReset(),
        ];
    }
}