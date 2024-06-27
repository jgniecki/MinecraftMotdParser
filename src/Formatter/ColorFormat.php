<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\Contracts\ColorFormatterInterface;
use DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface;

class ColorFormat implements HtmlFormatterInterface, ColorFormatterInterface
{
    use FormatTrait;

    private string $key;
    private string $colorName;
    private string $color;

    public function __construct(string $key, string $colorName, string $color)
    {
        $this->key = $key;
        $this->colorName = $colorName;
        $this->color = $color;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return 'color';
    }

    public function getColorName(): string
    {
        return $this->colorName;
    }

    public function getStyle(): string
    {
        return 'color: ' . $this->getColor() . ';';
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getTag(): string
    {
        return 'span';
    }
}
