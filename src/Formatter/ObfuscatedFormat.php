<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Formatter;

use DevLancer\MinecraftMotdParser\Contracts\FormatterInterface;

class ObfuscatedFormat implements FormatterInterface
{
    use FormatTrait;

    public function getKey(): string
    {
        return 'k';
    }

    public function getName(): string
    {
        return 'obfuscated';
    }
}
