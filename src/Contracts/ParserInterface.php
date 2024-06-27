<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser\Contracts;

use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;

interface ParserInterface
{
    /**
     * @param mixed $data
     */
    public function parse($data, MotdItemCollection $collection): MotdItemCollection;

    /**
     * @param mixed $data
     */
    public function supports($data): bool;
}
