<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface;

class StrikethroughFormat implements HtmlFormatterInterface
{
    use FormatTrait;

    public function getKey(): string
    {
        return 'm';
    }

    public function getName(): string
    {
        return 'strikethrough';
    }

    public function getStyle(): string
    {
        return 'text-decoration: line-through;';
    }

    public function getTag(): string
    {
        return 'span';
    }
}
