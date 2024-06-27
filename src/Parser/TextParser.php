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
    private bool $strictFormat;


    public function __construct(?FormatCollection $formatCollection = null, ?ColorCollection  $colorCollection = null, string $symbol = "§", bool $strictFormat = false)
    {
        $this->symbol = $symbol;
        $this->formatCollection = $formatCollection ?? FormatCollection::generate();
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
        $this->strictFormat = $strictFormat;
    }

    /**
     * @return bool
     */
    public function isStrictFormat(): bool
    {
        return $this->strictFormat;
    }

    /**
     * @param bool $strictFormat
     * @return void
     */
    public function setStrictFormat(bool $strictFormat): void
    {
        $this->strictFormat = $strictFormat;
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


        $strictFormat = ($this->strictFormat)? "" : "A-FKLMNOR";

        $regex = sprintf("/%s[0-9a-fklmnor%s]|[^%s]+/", $this->symbol, $strictFormat, $this->symbol);
        $regexKey = sprintf("/^%s([0-9a-fklmnor%s])$/", $this->symbol, $strictFormat);
        $lines = (array) \preg_split('/\\n/', $data);
        for ($i = 0; $i < \count($lines); $i++) {
            $motdItem = new MotdItem();
            $line = (string) $lines[$i];
            \preg_match_all($regex, $line, $output);

            if (empty($output[0]))
                continue;

            $values = $output[0];


            foreach ($values as $value) {
                preg_match($regexKey, $value, $match);
                $key = $match[1] ?? null;
                $motdItem = ($key == "r")? new MotdItem() : clone $motdItem;

                if ($key === null) {
                    if ($value != "0" && empty($value))
                        continue;

                    $motdItem->setText($value);
                    $collection->add($motdItem);
                    continue;
                }

                if ($motdItem->isReset())
                    $motdItem->setReset(false);

                if ($this->colorCollection->get($key)) {
                    $motdItem->setColor($key);
                } else {
                    $formatter = $this->formatCollection->get($key);
                    if (!$formatter)
                        continue; //todo nie rozpoznany format

                    $method = 'set' . \ucfirst($formatter->getName());
                    \call_user_func([$motdItem, $method], true);
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

    /**
     * @param string $data
     * @return bool
     */
    public function supports($data): bool
    {
        return \is_string($data) && !empty($data);
    }
}