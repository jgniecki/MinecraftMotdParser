<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser;

use BadMethodCallException;
use DevLancer\MinecraftMotdParser\Contracts\MotdItemInterface;

trait EntryValueMotdItemTrait
{
    /**
     * @param string $property
     * @param $value
     * @param MotdItemInterface $motdItem
     * @return MotdItemInterface
     * @throws BadMethodCallException
     */
    private function entryValueMotdItem(string $property, $value, MotdItemInterface $motdItem): MotdItemInterface
    {
        $method = 'set' . ucfirst($property);

        if (!method_exists($motdItem, $method)) {
            throw new BadMethodCallException(
                "Cannot set the value of property '$property' because the method '$method' does not exist."
            );
        }

        call_user_func([$motdItem, $method], $value);
        return $motdItem;
    }
}