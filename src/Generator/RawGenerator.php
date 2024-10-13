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
use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;
use DevLancer\MinecraftMotdParser\GetValueMotdItemTrait;

class RawGenerator implements GeneratorInterface
{
    use GetValueMotdItemTrait;

    private FormatCollection $formatCollection;
    private ColorCollection $colorCollection;
    private string $symbol;

    public function __construct(?FormatCollection $formatCollection = null, ?ColorCollection $colorCollection = null, string $symbol = '§')
    {
        $this->formatCollection = $formatCollection ?? FormatCollection::generate();
        $this->colorCollection = $colorCollection ?? ColorCollection::generate();
        $this->symbol = $symbol;
    }

    public function generate(MotdItemCollection $collection): string
    {
        $result = '';
        $lastColor = null;
        $lastFormat = [];

        for ($i = 0, $iMax = count($collection); $i < $iMax; ++$i) {
            $motdItem = $collection->get($i);
            if (!$motdItem) {
                continue;
            }

            $_item = '';
            $prevMotdItem = $collection->get($i - 1);
            if ($motdItem->isReset()) {
                $_item .= $this->concat(['r']);
                $lastColor = null;
                $lastFormat = [];
            }

            if ($prevMotdItem) {
                if ($this->isConflictFormat($prevMotdItem, $motdItem) && false === $motdItem->isReset()) {
                    $lastFormat = $this->getFormat($motdItem);
                    $lastColor  = $this->getColor($motdItem);
                    $_item .= $this->concat(array_merge(['r', $lastColor], $lastFormat));
                } else {
                    $formats = array_diff($this->getFormat($motdItem), $lastFormat);
                    $color = $this->getColor($motdItem);
                    if ($color && $color != $lastColor) {
                        $lastColor = $color;
                        $_item .= $this->concat([$color]);
                    }

                    if ($formats != []) {
                        $lastFormat = array_merge($lastFormat, $formats);
                        $_item .= $this->concat($formats);
                    }
                }
            } else {
                $lastFormat = $this->getFormat($motdItem);
                $lastColor  = $this->getColor($motdItem);
                $_item .= $this->concat(array_merge([$lastColor], $lastFormat));
            }

            if ($motdItem->getText()) {
                $_item .= $motdItem->getText();
                if (strpos($_item, "\n") !== false) {
                    $lastColor = null;
                    $lastFormat = [];
                }
            }

            $result .= $_item;
        }

        return $result;
    }

    private function concat(array $formats): string
    {
        $result = "";

        foreach ($formats as $format) {
            if (!$format) {
                continue;
            }

            $result .= $this->symbol . $format;
        }

        return $result;
    }

    private function getFormat(MotdItemInterface $motdItem): array
    {
        $formats = [];

        foreach ($this->formatCollection as $formatter) {
            if ($formatter->getKey() == 'r') {
                continue;
            }

            if ($this->getValueMotdItem($formatter->getName(), $motdItem) === true) {
                $formats[] = $formatter->getKey();
            }
        }

        return $formats;
    }

    private function getColor(MotdItemInterface $motdItem): ?string
    {
        if ($motdItem->getColor()) {
            $color = $this->colorCollection->get($motdItem->getColor());
            if ($color) {
                return $color->getKey();
            }
        }

        return null;
    }

    private function isConflictFormat(MotdItemInterface $motdItemLeft, MotdItemInterface $motdItemRight): bool
    {
        $motdItemLeft = clone $motdItemLeft;
        $motdItemRight = clone $motdItemRight;

        if ("\n" == $motdItemLeft->getText() || "\n" == $motdItemRight->getText()) {
            return false;
        }

        foreach ($this->formatCollection as $formatter) {
            if ($formatter->getKey() == 'r') {
                continue;
            }

            $leftValue = $this->getValueMotdItem($formatter->getName(), $motdItemLeft);
            $rightValue = $this->getValueMotdItem($formatter->getName(), $motdItemRight);

            if ($leftValue != $rightValue && $leftValue) {
                return true;
            }
        }

        return false;
    }
}
