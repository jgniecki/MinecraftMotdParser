<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Generator;

use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Contracts\GeneratorInterface;
use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;

class RawGenerator implements GeneratorInterface
{
    private string $symbol;

    public function __construct(string $symbol = '§')
    {
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
            $hasConflictFormat = false;
            if (!$motdItem->isReset() && $prevMotdItem && $this->hasConflictFormat($prevMotdItem, $motdItem)) {
                $_item .= $this->symbol . 'r';
                $hasConflictFormat = true;
            }

            if ($motdItem->isReset()) {
                $_item .= $this->symbol . 'r';
            }

            if ((($motdItem->isReset() || $hasConflictFormat) && $motdItem->getColor()) || ($motdItem->getColor() && !$this->prevHasColor($prevMotdItem, $motdItem->getColor()))) {
                $color = $motdItem->getColor();
                if (false === strpos($color, '#')) {
                    $_item .= $this->symbol . $motdItem->getColor();
                }
            }

            if ((($motdItem->isReset() || $hasConflictFormat) && $motdItem->isObfuscated()) || ($motdItem->isObfuscated() && !$this->prevHasFormat($prevMotdItem, 'isObfuscated'))) {
                $_item .= $this->symbol . 'k';
            }
            if ((($motdItem->isReset() || $hasConflictFormat) && $motdItem->isBold()) || ($motdItem->isBold() && !$this->prevHasFormat($prevMotdItem, 'isBold'))) {
                $_item .= $this->symbol . 'l';
            }
            if ((($motdItem->isReset() || $hasConflictFormat) && $motdItem->isStrikethrough()) || ($motdItem->isStrikethrough() && !$this->prevHasFormat($prevMotdItem, 'isStrikethrough'))) {
                $_item .= $this->symbol . 'm';
            }
            if ((($motdItem->isReset() || $hasConflictFormat) && $motdItem->isUnderlined()) || ($motdItem->isUnderlined() && !$this->prevHasFormat($prevMotdItem, 'isUnderlined'))) {
                $_item .= $this->symbol . 'n';
            }
            if ((($motdItem->isReset() || $hasConflictFormat) && $motdItem->isItalic()) || ($motdItem->isItalic() && !$this->prevHasFormat($prevMotdItem, 'isItalic'))) {
                $_item .= $this->symbol . 'o';
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

        if ($motdItemLeft->isBold() != $motdItemRight->isBold() && $motdItemLeft->isBold()) {
            return true;
        }

        if ($motdItemLeft->isItalic() != $motdItemRight->isItalic() && $motdItemLeft->isItalic()) {
            return true;
        }

        if ($motdItemLeft->isObfuscated() != $motdItemRight->isObfuscated() && $motdItemLeft->isObfuscated()) {
            return true;
        }

        if ($motdItemLeft->isStrikethrough() != $motdItemRight->isStrikethrough() && $motdItemLeft->isStrikethrough()) {
            return true;
        }

        if ($motdItemLeft->isUnderlined() != $motdItemRight->isUnderlined() && $motdItemLeft->isUnderlined()) {
            return true;
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
