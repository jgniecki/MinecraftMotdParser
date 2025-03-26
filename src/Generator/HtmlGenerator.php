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
use DevLancer\MinecraftMotdParser\Contracts\FormatterInterface;
use DevLancer\MinecraftMotdParser\Contracts\GeneratorInterface;
use DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface;
use DevLancer\MinecraftMotdParser\GetValueMotdItemTrait;

class HtmlGenerator implements GeneratorInterface
{
    use GetValueMotdItemTrait;

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
                    // Only allow valid hex color codes (without alpha channel), such as #FFF and #000000.
                    if(!preg_match('/^#(([0-9A-Fa-f]{2}){3}|[0-9A-Fa-f]{3})$/i', $motdItem->getColor())) {
                        continue;
                    }

                    $tags['span'][] = sprintf('color: %s;', $this->escape($motdItem->getColor()));
                } else {
                    $formatter = $this->colorCollection->get($motdItem->getColor());
                    if (!$formatter) {
                        continue;
                    }

                    $this->format($formatter, $tags, $value);
                }
            }

            foreach ($this->formatCollection as $formatter) {
                if (false === $this->getValueMotdItem($formatter->getName(), $motdItem)) {
                    continue;
                }

                $this->format($formatter, $tags, $value);
            }

            foreach ($tags as $tag => $styles) {
                $value = sprintf('<%s style="%s">%s</%s>', $tag, implode(' ', $styles), $value, $tag);
            }
            $value = sprintf($value, $this->escape($motdItem->getText()));
            $result .= $value;
        }

        return $result;
    }

    public function setFormatNewLine(string $format): void
    {
        $this->formatNewLine = $format;
    }

    protected function escape(string $text): string
    {
        return htmlentities($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function format(FormatterInterface $formatter, array &$tags, string &$value)
    {
        if ($formatter instanceof HtmlFormatterInterface) {
            $tags[$formatter->getTag()][] = $formatter->getStyle();
        } else {
            $value = sprintf($value, $formatter->getFormat());
        }
    }
}
