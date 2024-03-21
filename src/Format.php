<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser;

class Format
{
    public const FORMAT = [
        'k' => 'obfuscated',
        'l' => 'bold',
        'm' => 'strikethrough',
        'n' => 'underlined',
        'o' => 'italic',
        'r' => 'reset'
    ];
}