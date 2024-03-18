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
    public const COLOR = [
        'black'        => '#000000',
        'dark_blue'    => '#0000AA',
        'dark_green'   => '#00AA00',
        'dark_aqua'    => '#00AAAA',
        'dark_red'     => '#AA0000',
        'dark_purple'  => '#AA00AA',
        'gold'         => '#FFAA00',
        'gray'         => '#AAAAAA',
        'dark_gray'    => '#555555',
        'blue'         => '#5555FF',
        'green'        => '#55FF55',
        'aqua'         => '#55FFFF',
        'red'          => '#FF5555',
        'light_purple' => '#FF55FF',
        'yellow'       => '#FFFF55',
        'white'        => '#FFFFFF'
    ];

    public const COLOR_LIST = [
        '0' => self::COLOR['black'],
        '1' => self::COLOR['dark_blue'],
        '2' => self::COLOR['dark_green'],
        '3' => self::COLOR['dark_aqua'],
        '4' => self::COLOR['dark_red'],
        '5' => self::COLOR['dark_purple'],
        '6' => self::COLOR['gold'],
        '7' => self::COLOR['gray'],
        '8' => self::COLOR['dark_gray'],
        '9' => self::COLOR['blue'],
        'a' => self::COLOR['green'],
        'b' => self::COLOR['aqua'],
        'c' => self::COLOR['red'],
        'd' => self::COLOR['light_purple'],
        'e' => self::COLOR['yellow'],
        'f' => self::COLOR['white'],
    ];

    public const FORMAT = [
        'k' => 'obfuscated',
        'l' => 'bold',
        'm' => 'strikethrough',
        'n' => 'underlined',
        'o' => 'italic',
        'r' => 'reset'
    ];

    public static function isColor(string $color): bool
    {
        if (strpos($color, '§') !== false) {
            $key = substr($color, strpos($color, '§')+2, 1);
            return isset(self::COLOR_LIST[$key]);
        }

        if (strpos($color, '\u00A7') !== false) {
            $key = substr($color, strpos($color, '\u00A7')+1, 1);
            return isset(self::COLOR_LIST[$key]);
        }

        return false;
    }

    public static function isFormat(string $format): bool
    {
        if (strpos($format, '§') !== false) {
            $key = substr($format, strpos($format, '§')+2, 1);
            return isset(self::FORMAT[$key]);
        }

        if (strpos($format, '\u00A7') !== false) {
            $key = substr($format, strpos($format, '\u00A7')+1, 1);
            return isset(self::FORMAT[$key]);
        }

        return false;
    }

    public static function getColorHex(string $color): ?string
    {
        if (strpos($color, '#') !== false)
            return $color;

        if (strpos($color, '§') !== false) {
            $key = substr($color, strpos($color, '§')+2, 1);
            return self::COLOR_LIST[$key];
        }

        if (strpos($color, '\u00A7') !== false) {
            $key = substr($color, strpos($color, '\u00A7')+1, 1);
            return self::COLOR_LIST[$key];
        }

        return self::COLOR[$color] ?? null;
    }
}