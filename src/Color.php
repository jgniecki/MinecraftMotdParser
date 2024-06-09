<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser;

class Color
{
    public const COLOR_NAME = [
        'black'        => '0',
        'dark_blue'    => '1',
        'dark_green'   => '2',
        'dark_aqua'    => '3',
        'dark_red'     => '4',
        'dark_purple'  => '5',
        'gold'         => '6',
        'gray'         => '7',
        'dark_gray'    => '8',
        'blue'         => '9',
        'green'        => 'a',
        'aqua'         => 'b',
        'red'          => 'c',
        'light_purple' => 'd',
        'yellow'       => 'e',
        'white'        => 'f'
    ];

    public const COLOR_HEX = [
        '0' => '#000000',
        '1' => '#0000AA',
        '2' => '#00AA00',
        '3' => '#00AAAA',
        '4' => '#AA0000',
        '5' => '#AA00AA',
        '6' => '#FFAA00',
        '7' => '#AAAAAA',
        '8' => '#555555',
        '9' => '#5555FF',
        'a' => '#55FF55',
        'b' => '#55FFFF',
        'c' => '#FF5555',
        'd' => '#FF55FF',
        'e' => '#FFFF55',
        'f' => '#FFFFFF'
    ];

    public static function getColorCode(string $color): ?string
    {
        if (isset(self::COLOR_NAME[$color]))
            return self::COLOR_NAME[$color];

        if (isset(self::COLOR_HEX[$color]))
            return $color;

        foreach (self::COLOR_HEX as $key => $hex) {
            if (strtoupper($color) == $hex)
                return $key;
        }

        return null;
    }
}