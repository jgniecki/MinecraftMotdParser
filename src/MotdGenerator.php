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
    /**
     * @var MotdItemInterface|MotdItemInterface[]
     */
    private array $motdItems;


    /**
     * @param MotdItemInterface[]|MotdItemInterface $container
     */
    public function __construct($container)
    {
        if ($container instanceof MotdItemInterface)
            $this->motdItems = [$container];
        else
            $this->motdItems = $container;
    }

    public function html(FormatCollection $formatCollection, ColorCollection $colorCollection): string
    {
        $result = "";
        /**
         * @var MotdItemInterface $container
         */
        foreach ($this->motdItems as $item) {
            if (!$item->getText())
                continue;

            if ($item->getText() == "\n") {
                $result .= "<br />";
                continue;
            }

            $value = "%s";
            $tags = [];

            foreach ($formatCollection as $format) {
                $method = "is" . \ucfirst($format->getName());
                if (\call_user_func_array([$item, $method], []) === false)
                    continue;

                if ($format instanceof HtmlFormatterInterface) {
                    $tags[$format->getTag()][] = $format->getStyle();
                } else {
                    $value = \sprintf($value, $format->getFormat());
                }
            }

            if ($item->getColor()) {
                if (\strpos($item->getColor(), '#') !== false) {
                    $value = \sprintf("<span style='color: %s;'>%s</span>", $item->getColor(), $value);
                } else {
                    $color = $colorCollection->get($item->getColor());
                    if ($color instanceof HtmlFormatterInterface) {
                        $tags[$color->getTag()][] = $color->getStyle();
                    } else {
                        $value = \sprintf($value, $color->getFormat());
                    }
                }
            }

            foreach ($tags as $tag => $styles) {
                $value = \sprintf("<%s style=\"%s\">%s</%s>", $tag, implode(" ", $styles), $value, $tag);
            }
            $value = \sprintf($value, $item->getText());
            $result .= $value;
        }

        return $result;
    }

    public function text(): string
    {
        $result = "";

        /**
         * @var MotdItemInterface $item
         */
        foreach ($this->motdItems as $item) {
            if  ($item->getText())
                $result .= $item->getText();
        }

        return $result;
    }

    public function raw(): string
    {
        $result = "";

        /**
         * @var MotdItemInterface $item
         */
        foreach ($this->motdItems as $item) {
            $_item = "";
            if ($item->getColor()) {
                $color = $item->getColor();
                if (strpos($color, '#') !== false)
                    $_item .= '§' . $item->getColor();
            }
            if ($item->isObfuscated())
                $_item .= '§k';
            if ($item->isBold())
                $_item .= '§l';
            if ($item->isStrikethrough())
                $_item .= '§m';
            if ($item->isUnderlined())
                $_item .= '§n';
            if ($item->isItalic())
                $_item .= '§o';
            if ($item->isReset())
                $_item .= '§r';
            if  ($item->getText())
                $_item .= $item->getText();

            $result .= $_item;
        }

        return $result;
    }
}