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

trait GetValueMotdItemTrait
{
    /**
     * @param string $property
     * @param MotdItemInterface $motdItem
     * @return mixed
     * @throws BadMethodCallException
     */
    private function getValueMotdItem(string $property, MotdItemInterface $motdItem)
    {
        if (method_exists($motdItem, 'is' . ucfirst($property))) {
            return (bool)call_user_func([$motdItem, 'is' . ucfirst($property)]);
        }

        if (method_exists($motdItem, 'get' . ucfirst($property))) {
            return call_user_func([$motdItem, 'get' . ucfirst($property)]);
        }

        throw new BadMethodCallException(
            "Cannot retrieve the value of property '$property' because neither 'get" . ucfirst($property) . "' nor 'is" . ucfirst($property) . "' method exists."
        );    }
}