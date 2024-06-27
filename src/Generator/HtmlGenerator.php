<?php

namespace DevLancer\MinecraftMotdParser\Generator;

use DevLancer\MinecraftMotdParser\ColorCollection;
use DevLancer\MinecraftMotdParser\Contracts\GeneratorInterface;
use DevLancer\MinecraftMotdParser\Contracts\HtmlFormatterInterface;
use DevLancer\MinecraftMotdParser\FormatCollection;
use DevLancer\MinecraftMotdParser\MotdItemCollection;

class HtmlGenerator implements GeneratorInterface
{
    private FormatCollection $formatCollection;
    private ColorCollection  $colorCollection;

    private string $formatNewLine = "%s<br />";


    public function __construct(?FormatCollection $formatCollection = null, ?ColorCollection  $colorCollection = null)
    {
        $this->formatCollection = $formatCollection ?? FormatCollection::generate();
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
    }

    public function setFormatNewLine(string $format): void
    {
        $this->formatNewLine = $format;
    }

    public function generate(MotdItemCollection $collection): string
    {
        $result = "";
        foreach ($collection as $motdItem) {
            if (!$motdItem->getText())
                continue;

            if ($motdItem->getText() == "\n") {
                $result = \sprintf($this->formatNewLine, $result);
                continue;
            }

            $value = "%s";
            $tags = [];

            if ($motdItem->getColor()) {
                if (\strpos($motdItem->getColor(), '#') !== false) {
                    $tags['span'][] = \sprintf("color: %s;", $motdItem->getColor());
                } else {
                    $color = $this->colorCollection->get($motdItem->getColor());
                    if (!$color)
                        continue;

                    if ($color instanceof HtmlFormatterInterface) {
                        $tags[$color->getTag()][] = $color->getStyle();
                    } else {
                        $value = \sprintf($value, $color->getFormat());
                    }
                }
            }

            foreach ($this->formatCollection as $format) {
                $method = "is" . \ucfirst($format->getName());
                if (\call_user_func([$motdItem, $method]) === false)
                    continue;

                if ($format instanceof HtmlFormatterInterface) {
                    $tags[$format->getTag()][] = $format->getStyle();
                } else {
                    $value = \sprintf($value, $format->getFormat());
                }
            }

            foreach ($tags as $tag => $styles) {
                $value = \sprintf("<%s style=\"%s\">%s</%s>", $tag, implode(" ", $styles), $value, $tag);
            }
            $value = \sprintf($value, $motdItem->getText());
            $result .= $value;
        }

        return $result;
    }
}