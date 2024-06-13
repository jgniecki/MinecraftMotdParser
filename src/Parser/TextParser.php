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


    public function __construct(?FormatCollection $formatCollection = null, ?ColorCollection  $colorCollection = null, string $symbol = "§")
    {
        $this->symbol = $symbol;
        $this->formatCollection = $formatCollection ?? FormatCollection::generate();
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
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

        if ($data == "\n") {
            $newLine = new MotdItem();
            $newLine->setText("\n");
            $collection->add($newLine);
            return $collection;
        }

        $regex = "/" . $this->symbol . "([0-9a-fklmnor])(.*?)(?=" . $this->symbol . "[0-9a-fklmnor]|$)/";

        $lines = \preg_split('/\\n/', $data);
        for ($i = 0; $i < \count($lines); $i++) {
            $motdItem = new MotdItem();
            $line = $lines[$i];
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
            if ($i+1 < \count($lines)) {
                $newLine = new MotdItem();
                $newLine->setText("\n");
                $collection->add($newLine);
            }
        }

        return $collection;
    }

    public function supports($data): bool
    {
        return \is_string($data) && !empty($data);
    }
}