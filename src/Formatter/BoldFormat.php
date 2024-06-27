<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface;

class BoldFormat implements HtmlFormatterInterface
{
    use FormatTrait;

    public function getKey(): string
    {
        return 'l';
    }

    public function getName(): string
    {
        return 'bold';
    }

    public function getStyle(): string
    {
        return 'font-weight: bold;';
    }

    public function getTag(): string
    {
        return 'span';
    }
}
