<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Generator;

use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Contracts\GeneratorInterface;
use DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface;

class HtmlGenerator implements GeneratorInterface
{
    private FormatCollection $formatCollection;
    private ColorCollection $colorCollection;

    private string $formatNewLine = '%s<br />';

    public function __construct(?FormatCollection $formatCollection = null, ?ColorCollection $colorCollection = null)
    {
        $this->formatCollection = $formatCollection ?? FormatCollection::generate();
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
    }

    public function generate(MotdItemCollection $collection): string
    {
        $result = '';
        foreach ($collection as $motdItem) {
            if (!$motdItem->getText()) {
                continue;
            }

            if ("\n" == $motdItem->getText()) {
                $result = sprintf($this->formatNewLine, $result);

                continue;
            }

            $value = '%s';
            $tags = [];

            if ($motdItem->getColor()) {
                if (str_contains($motdItem->getColor(), '#')) {
                    $tags['span'][] = sprintf('color: %s;', $motdItem->getColor());
                } else {
                    $color = $this->colorCollection->get($motdItem->getColor());
                    if (!$color) {
                        continue;
                    }

                    if ($color instanceof HtmlFormatterInterface) {
                        $tags[$color->getTag()][] = $color->getStyle();
                    } else {
                        $value = sprintf($value, $color->getFormat());
                    }
                }
            }

            foreach ($this->formatCollection as $format) {
                $method = 'is' . ucfirst($format->getName());
                if (false === call_user_func([$motdItem, $method])) {
                    continue;
                }

                if ($format instanceof HtmlFormatterInterface) {
                    $tags[$format->getTag()][] = $format->getStyle();
                } else {
                    $value = sprintf($value, $format->getFormat());
                }
            }

            foreach ($tags as $tag => $styles) {
                $value = sprintf('<%s style="%s">%s</%s>', $tag, implode(' ', $styles), $value, $tag);
            }
            $value = sprintf($value, $motdItem->getText());
            $result .= $value;
        }

        return $result;
    }

    public function setFormatNewLine(string $format): void
    {
        $this->formatNewLine = $format;
    }
}
