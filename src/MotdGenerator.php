<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser;

class MotdGenerator
{
    private array $container;

    /**
     * @param Container[]|Container$container
     */
    public function __construct($container)
    {
        if ($container instanceof Container)
            $this->container = [$container];
        else
            $this->container = $container;
    }

    public function html(): string
    {
        $result = "";
        /**
         * @var Container $container
         */
        foreach ($this->container as $container) {
            if (!$container->getText())
                continue;

            if ($container->getText() == "\n") {
                $result .= "<br />";
                continue;
            }

            $_item = $container->getText();

            if ($container->isBold())
                $_item = sprintf("<b>%s</b>", $_item);

            if ($container->isStrikethrough())
                $_item = sprintf("<s>%s</s>", $_item);

            if ($container->isUnderlined())
                $_item = sprintf("<u>%s</u>", $_item);

            if ($container->isItalic())
                $_item = sprintf("<i>%s</i>", $_item);

            if ($container->getColor()) {
                $color = $container->getColor();
                if (strpos($color, '#') === false) {
                    $color = Color::COLOR_HEX[$container->getColor()];
                }

                $_item = sprintf("<span style='color: %s;'>%s</span>", $color, $_item);
            }

            $result .= $_item;
        }

        return $result;
    }

    public function text(): string
    {
        $result = "";

        /**
         * @var Container $container
         */
        foreach ($this->container as $container) {
            if  ($container->getText())
                $result .= $container->getText();
        }

        return $result;
    }

    public function raw(): string
    {
        $result = "";

        /**
         * @var Container $container
         */
        foreach ($this->container as $container) {
            $_item = "";
            if ($container->getColor()) {
                $color = $container->getColor();
                if (strpos($color, '#') !== false)
                    $_item .= '§' . $container->getColor();
            }
            if ($container->isObfuscated())
                $_item .= '§k';
            if ($container->isBold())
                $_item .= '§l';
            if ($container->isStrikethrough())
                $_item .= '§m';
            if ($container->isUnderlined())
                $_item .= '§n';
            if ($container->isItalic())
                $_item .= '§o';
            if ($container->isReset())
                $_item .= '§r';
            if  ($container->getText())
                $_item .= $container->getText();

            $result .= $_item;
        }

        return $result;
    }
}