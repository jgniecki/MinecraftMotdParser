<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Parser;

use DevLancer\MinecraftMotdParser\Collection\ColorCollection;
use DevLancer\MinecraftMotdParser\Collection\FormatCollection;
use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Contracts\ColorFormatterInterface;
use DevLancer\MinecraftMotdParser\Contracts\FormatterInterface;
use DevLancer\MinecraftMotdParser\Contracts\ParserInterface;
use DevLancer\MinecraftMotdParser\EntryValueMotdItemTrait;
use DevLancer\MinecraftMotdParser\MotdItem;
use InvalidArgumentException;

class TextParser implements ParserInterface
{
    use EntryValueMotdItemTrait;
    private string $symbol;
    private FormatCollection $formatCollection;
    private ColorCollection $colorCollection;

    /**
     * @var array<string, FormatterInterface>
     */
    private array $collection;
    private bool $strictFormat;

    public function __construct(?FormatCollection $formatCollection = null, ?ColorCollection $colorCollection = null, string $symbol = '§', bool $strictFormat = false)
    {
        $this->symbol = $symbol;
        $this->formatCollection = $formatCollection ?? FormatCollection::generate();
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
        $this->strictFormat = $strictFormat;

        $this->generateCollection();
    }

    public function isStrictFormat(): bool
    {
        return $this->strictFormat;
    }

    public function setStrictFormat(bool $strictFormat): void
    {
        $this->strictFormat = $strictFormat;
    }

    /**
     * @param string $data
     */
    public function parse($data, MotdItemCollection $collection): MotdItemCollection
    {
        if (!$this->supports($data)) {
            throw new InvalidArgumentException('Unsupported data');
        }

        if ("\n" == $data) {
            $newLine = new MotdItem();
            $newLine->setText("\n");
            $collection->add($newLine);

            return $collection;
        }

        $formatKeys = $this->generateFormatKeys();
        $regex = sprintf('/%s[%s]|[^%s]+/', $this->symbol, $formatKeys, $this->symbol);
        $regexKey = sprintf('/^%s([%s])$/', $this->symbol, $formatKeys);
        $lines = (array)preg_split('/\n/', $data);
        for ($i = 0, $iMax = count($lines); $i < $iMax; ++$i) {
            $motdItem = new MotdItem();
            $line = (string)$lines[$i];
            preg_match_all($regex, $line, $motd);
            if (empty($motd[0])) {
                continue;
            }

            $partsMotd = $motd[0];
            foreach ($partsMotd as $item) {
                preg_match($regexKey, $item, $match);
                $key = $match[1] ?? null;
                $motdItem = ('r' == $key) ? new MotdItem() : clone $motdItem;

                if (null === $key) {
                    if (strlen($item) == 0 && empty($item)) {
                        continue;
                    }

                    $motdItem->setText($item);
                    $collection->add($motdItem);

                    if ($motdItem->isReset()) {
                        $motdItem = clone $motdItem;
                        $motdItem->setReset(false);
                    }

                    continue;
                }

                $formatter = $this->collection[$key] ?? null;
                if ($formatter) {
                    $set = ($formatter instanceof ColorFormatterInterface)? $formatter->getKey() : true;
                    $motdItem = $this->entryValueMotdItem($formatter->getName(), $set, $motdItem);
                }
            }

            if ($i + 1 < count($lines)) {
                $newLine = new MotdItem();
                $newLine->setText("\n");
                $collection->add($newLine);
            }
        }

        return $collection;
    }

    /**
     * @param string $data
     */
    public function supports($data): bool
    {
        return is_string($data) && !empty($data);
    }

    private function generateCollection()
    {
        $formats = array_merge($this->colorCollection->all(), $this->formatCollection->all());

        foreach ($formats as $item) {
            $this->collection[$item->getKey()] = $item;

            if (false === $this->strictFormat) {
                $this->collection[strtoupper($item->getKey())] = $item;
            }
        }
    }

    private function generateFormatKeys(): string
    {
        return implode("", array_keys($this->collection));
    }
}
