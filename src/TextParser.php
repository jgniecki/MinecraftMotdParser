<?php

namespace DevLancer\MinecraftMotdParser;

class TextParser
{
    private string $char;
    private FormatCollection $formatCollection;
    private ColorCollection  $colorCollection;


    public function __construct(FormatCollection $formatCollection, ColorCollection  $colorCollection, string $char = "§")
    {
        $this->char = $char;
        $this->formatCollection = $formatCollection;
        $this->colorCollection = $colorCollection;
    }

    public function parse(string $content, MotdItemInterface $container): array
    {
        $list = [];
        $motdItem = clone $container;
        $regex = "/" . $this->char . "([0-9a-fklmnor])(.*?)(?=" . $this->char . "[0-9a-fklmnor]|$)/";
        $lines = \preg_split('/\\n/', $content);
        foreach ($lines as $line) {
            \preg_match_all($regex, $line, $output);

            if (!isset($output[1]) || !isset($output[2]))
                continue;

            $keys = $output[1];
            $values = $output[2];

            foreach ($keys as $id => $key) {
                $motdItem = ($key == "r")? clone $container : clone $motdItem;

                if ($this->colorCollection->get($key)) {
                    $motdItem->setColor($key);
                } else {
                    $method = 'set' . \ucfirst($this->formatCollection->get($key)->getName());
                    \call_user_func([$motdItem, $method], true);
                }

                if (!empty($values[$id])) {
                    $motdItem->setText($values[$id]);
                    $list[] = $motdItem;
                }
            }

            $newLine = clone $container;
            $newLine->setText("\n");
            $list[] = $newLine;
        }

        return $list;
    }
}