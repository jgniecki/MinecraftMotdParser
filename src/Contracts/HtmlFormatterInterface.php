<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Contracts;

interface HtmlFormatterInterface extends FormatterInterface
{
    /**
     * Get css style used to format the tag (eq. 'color: #ffffff;').
     */
    public function getStyle(): string;

    /**
     * Get html tag in which the format will be generated (eq. span).
     */
    public function getTag(): string;
}
