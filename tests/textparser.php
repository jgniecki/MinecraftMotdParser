<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use DevLancer\MinecraftMotdParser\TextParser;
use DevLancer\MinecraftStatus\Ping;

require_once '../vendor/autoload.php';

$textParser = new TextParser();

$list = [
//    'BGMC.GRAJ.TODAY',
//    'hypemc.ovh',
//    'nightcraft.pl',
//    'realcraft.pl',
//    'anarchia.gg',
//    'craftmc.pl',
//    'slaycraft.pl',
//    'mc-vision.pl',
//    'mc.bfsmc.pl',
];

foreach ($list as $server) {
    $ping = new Ping($server);
    try {
        $ping->connect();
        if ($ping->getPlayers() == [])
            continue;

        echo "<br>\n\n".$server."\n<br>";
        foreach ($ping->getPlayers() as $player) {
            $data = $textParser->parse($player);
//            foreach ($data as $item)
//                var_dump($item->jsonSerialize());
            foreach ($data as $item)
                echo (string)$item;
        }



    } catch (\DevLancer\MinecraftStatus\Exception\Exception $exception) {
        echo $exception->getMessage();
        continue;
    }
}
//$data = $textParser->parse("§d§lɴɪɢʜᴛᴄʀᴀғᴛ.ᴘʟ §7>> §7[1.16-1.20] §e☄");
//foreach ($data as $item)
//    echo (string) $item;
