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

class TextGenerator implements GeneratorInterface
{
    public function generate(MotdItemCollection $collection): string
    {
        $result = '';

        foreach ($collection as $motdItem) {
            if ($motdItem->getText()) {
                $result .= $motdItem->getText();
            }
        }

        return $result;
    }
}
