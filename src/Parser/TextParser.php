<?php

namespace DevLancer\MinecraftMotdParser\Parser;

use DevLancer\MinecraftMotdParser\ColorCollection;
use DevLancer\MinecraftMotdParser\Contracts\ParserInterface;
use DevLancer\MinecraftMotdParser\FormatCollection;
use DevLancer\MinecraftMotdParser\MotdItem;
use DevLancer\MinecraftMotdParser\MotdItemCollection;

class TextParser implements ParserInterface
{
    private string $symbol;
    private FormatCollection $formatCollection;
    private ColorCollection  $colorCollection;


    public function __construct(FormatCollection $formatCollection, ColorCollection  $colorCollection, string $symbol = "§")
    {
        $this->symbol = $symbol;
        $this->formatCollection = $formatCollection;
        $this->colorCollection = $colorCollection;
    }

    /**
     * @param string $data
     * @param MotdItemCollection $collection
     * @return MotdItemCollection
     */
    public function parse($data, MotdItemCollection $collection): MotdItemCollection
    {
        if (!$this->supports($data)) {
            throw new \InvalidArgumentException("Unsupported data");
        }

        $motdItem = new MotdItem();
        $regex = "/" . $this->symbol . "([0-9a-fklmnor])(.*?)(?=" . $this->symbol . "[0-9a-fklmnor]|$)/";
        $lines = \preg_split('/\\n/', $data);
        foreach ($lines as $line) {
            \preg_match_all($regex, $line, $output);

            if (!isset($output[1]) || !isset($output[2]))
                continue;

            $keys   = $output[1];
            $values = $output[2];

            foreach ($keys as $id => $key) {
                $motdItem = ($key == "r")? new MotdItem() : clone $motdItem;

                if ($this->colorCollection->get($key)) {
                    $motdItem->setColor($key);
                } else {
                    $method = 'set' . \ucfirst($this->formatCollection->get($key)->getName());
                    \call_user_func([$motdItem, $method], true);
                }

                if (!empty($values[$id])) {
                    $motdItem->setText($values[$id]);
                    $collection->add($motdItem);
                    if ($key == "r")
                        $motdItem = new MotdItem();
                }
            }

            $newLine = new MotdItem();
            $newLine->setText("\n");
            $collection->add($newLine);
        }

        return $collection;
    }

    public function supports($data): bool
    {
        return is_string($data);
    }
}