<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Contracts;

interface ColorFormatterInterface extends FormatterInterface
{
    /**
     * Get the hex color.
     */
    public function getColor(): string;

    /**
     * Get the color name of the formatter (eg. black, aqua, dark_red).
     */
    public function getColorName(): string;
}
