<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser;

use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;
use JsonSerializable;

class MotdItem implements JsonSerializable, MotdItemInterface
{
    public ?string $text = null;
    private ?string $color = null;
    private bool $obfuscated = false;
    private bool $bold = false;
    private bool $strikethrough = false;
    private bool $underlined = false;
    private bool $italic = false;
    private bool $reset = false;

    /**
     * @return array<string, null|bool|string>
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function isBold(): bool
    {
        return $this->bold;
    }

    public function setBold(bool $bold): void
    {
        $this->bold = $bold;
    }

    public function isItalic(): bool
    {
        return $this->italic;
    }

    public function setItalic(bool $italic): void
    {
        $this->italic = $italic;
    }

    public function isObfuscated(): bool
    {
        return $this->obfuscated;
    }

    public function setObfuscated(bool $obfuscated): void
    {
        $this->obfuscated = $obfuscated;
    }

    public function isUnderlined(): bool
    {
        return $this->underlined;
    }

    public function setUnderlined(bool $underlined): void
    {
        $this->underlined = $underlined;
    }

    public function isStrikethrough(): bool
    {
        return $this->strikethrough;
    }

    public function setStrikethrough(bool $strikethrough): void
    {
        $this->strikethrough = $strikethrough;
    }

    public function isReset(): bool
    {
        return $this->reset;
    }

    public function setReset(bool $reset): void
    {
        $this->reset = $reset;
    }
}
