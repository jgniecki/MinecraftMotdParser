<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use DevLancer\MinecraftMotdParser\ArrayParser;
use DevLancer\MinecraftStatus\Ping;


require_once dirname(__DIR__) . '/vendor/autoload.php';

$parser = new ArrayParser();
$ping = new Ping('FantasyCraft.PL');
$time = microtime();
$ping->connect();
//$time = microtime() - $time;
//var_dump($time * 1000);
//$parser->parse($ping->getInfo()['description']);
foreach ($parser->parse($ping->getInfo()['description']) as $item) {
//    print_r($item->jsonSerialize());
    echo (string) $item;
}