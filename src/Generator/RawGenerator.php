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

class RawGenerator implements GeneratorInterface
{
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

        for ($i = 0, $iMax = count($collection); $i < $iMax; ++$i) {
            $motdItem = $collection->get($i);
            if (!$motdItem) {
                continue;
            }

            $_item = '';
            $prevMotdItem = $collection->get($i - 1);
            $hasReset = false;
            if (!$motdItem->isReset() && $prevMotdItem && $this->hasConflictFormat($prevMotdItem, $motdItem)) {
                $_item .= $this->symbol . 'r';
                $hasReset = true;
            }

            if ($motdItem->isReset()) {
                $_item .= $this->symbol . 'r';
                $hasReset = true;
            }

            if (($hasReset && $motdItem->getColor()) || ($motdItem->getColor() && !$this->prevHasColor($prevMotdItem, $motdItem->getColor()))) {
                $color = $motdItem->getColor();
                if (false === str_contains($color, '#')) {
                    $_item .= $this->symbol . $this->colorCollection->get($motdItem->getColor())->getKey();
                }
            }

            foreach ($this->formatCollection as $format) {
                if ($format->getKey() == 'r') {
                    continue;
                }

                $method = 'is' . ucfirst($format->getName());
                if (($hasReset && $motdItem->{$method}()) || ($motdItem->{$method}() && !$this->prevHasFormat($prevMotdItem, $method))) {
                    $_item .= $this->symbol . $format->getKey();
                }
            }

            if ($motdItem->getText()) {
                $_item .= $motdItem->getText();
            }

            $result .= $_item;
        }

        return $result;
    }

    private function hasConflictFormat(MotdItemInterface $motdItemLeft, MotdItemInterface $motdItemRight): bool
    {
        $motdItemLeft = clone $motdItemLeft;
        $motdItemRight = clone $motdItemRight;

        if ("\n" == $motdItemLeft->getText() || "\n" == $motdItemRight->getText()) {
            return false;
        }

        foreach ($this->formatCollection as $format) {
            if ($format->getKey() == 'r') {
                continue;
            }

            $method = 'is' . ucfirst($format->getName());
            if ($motdItemLeft->{$method}() != $motdItemRight->{$method}() && $motdItemLeft->{$method}()) {
                return true;
            }
        }

        return false;
    }

    private function prevHasColor(?MotdItemInterface $motdItem, string $color): bool
    {
        if (null == $motdItem) {
            return false;
        }

        if ("\n" == $motdItem->getText()) {
            return false;
        }

        return $motdItem->getColor() == $color;
    }

    private function prevHasFormat(?MotdItemInterface $motdItem, string $formatMethod): bool
    {
        if (null == $motdItem) {
            return false;
        }

        if ("\n" == $motdItem->getText()) {
            return false;
        }

        return $motdItem->{$formatMethod}();
    }
}
