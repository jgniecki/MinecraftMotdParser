<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Contracts;

/**
 * Interface FormatterInterface.
 */
interface FormatterInterface
{
    /**
     * Get the key of the formatter (eg. k, 1, c).
     */
    public function getKey(): string;

    /**
     * Get the name of the formatter (eg. bold, underlined, color, italic, reset, obfuscated, strikethrough).
     */
    public function getName(): string;

    /**
     * Get the format pattern used in the sprintf function.
     */
    public function getFormat(): string;
}
