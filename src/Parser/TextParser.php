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
use DevLancer\MinecraftMotdParser\Contracts\ParserInterface;
use DevLancer\MinecraftMotdParser\MotdItem;
use InvalidArgumentException;

class TextParser implements ParserInterface
{
    private string $symbol;
    private FormatCollection $formatCollection;
    private ColorCollection $colorCollection;
    private bool $strictFormat;

    public function __construct(?FormatCollection $formatCollection = null, ?ColorCollection $colorCollection = null, string $symbol = '§', bool $strictFormat = false)
    {
        $this->symbol = $symbol;
        $this->formatCollection = $formatCollection ?? FormatCollection::generate();
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
        $this->strictFormat = $strictFormat;
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

        $strictFormat = ($this->strictFormat) ? '' : 'A-FKLMNOR';

        $regex = sprintf('/%s[0-9a-fklmnor%s]|[^%s]+/', $this->symbol, $strictFormat, $this->symbol);
        $regexKey = sprintf('/^%s([0-9a-fklmnor%s])$/', $this->symbol, $strictFormat);
        $lines = (array)preg_split('/\n/', $data);
        for ($i = 0; $i < count($lines); ++$i) {
            $motdItem = new MotdItem();
            $line = (string)$lines[$i];
            preg_match_all($regex, $line, $output);

            if (empty($output[0])) {
                continue;
            }

            $values = $output[0];

            foreach ($values as $value) {
                preg_match($regexKey, $value, $match);
                $key = $match[1] ?? null;
                $motdItem = ('r' == $key) ? new MotdItem() : clone $motdItem;

                if (null === $key) {
                    if ('0' != $value && empty($value)) {
                        continue;
                    }

                    $motdItem->setText($value);
                    $collection->add($motdItem);

                    continue;
                }

                if ($motdItem->isReset()) {
                    $motdItem->setReset(false);
                }

                if ($this->colorCollection->get($key)) {
                    $motdItem->setColor($key);
                } else {
                    $formatter = $this->formatCollection->get($key);
                    if (!$formatter) {
                        continue;
                    } // todo nie rozpoznany format

                    $method = 'set' . ucfirst($formatter->getName());
                    call_user_func([$motdItem, $method], true);
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
}
