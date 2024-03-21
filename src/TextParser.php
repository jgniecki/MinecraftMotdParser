<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser;

class TextParser
{
    private array $list = [];
    private function generate(string $text): void
    {
        if ($text == "\n") {
            $container = new Container();
            $container->setText("\n");
            $this->list[] = $container;
            return;
        }

        $data = \explode("\n", $text);
        $nElem = substr_count($text, "\n");
        $elem = 1;

        foreach ($data as $item) {
            if (empty($item)) {
                $this->generate("\n");
                $elem++;
                continue;
            }

            $container = new Container();
            if ($item == "§" || strpos($item, '§') === false) {
                $container->setText($item);
                $this->list[] = $container;
                $elem++;
                continue;
            }

            $match = explode('§', $item);
            foreach ($match as $key => $part) {
                $container = clone $container;
                $code = $part[0];
                $content = "";

                if (strlen($part) > 1)
                    $content = substr($part, 1);

                $container->setText($content);
                if (isset(Format::FORMAT[$code])) {
                    if ($code == 'r') {
                        $container = new Container();
                        $container->setReset(true);
                        $container->setText($content);
                        $this->list[] = $container;
                        $container = new Container();
                        continue;
                    }

                    if ($code == "k") {
                        $container->setObfuscated(true);
                    } else if ($code == "l") {
                        $container->setBold(true);
                    } else if ($code == "m") {
                        $container->setStrikethrough(true);
                    } else if ($code == "n") {
                        $container->setUnderlined(true);
                    } else if ($code == "o") {
                        $container->setItalic(true);
                    }
                }else if (isset(Color::COLOR_HEX[$code])) {
                    $container->setColor($code);
                    if (empty($content))
                        continue;
                } else {
                    //przemysl to jeszcze, czy zawsze
                    $content = $code . $content;
                    if ($key > 0)
                        $content = '§' . $content;

                    $container->setText($content);
                }

                $this->list[] = $container;
                $container->setObfuscated(false); //czy to tak ma byc?
            }

            if ($elem <= $nElem)
                $this->generate("\n");

            $elem++;
        }


    }

    public function parse(string $text): array
    {
        $this->list = [];
        $this->generate($text);
        return $this->list;
    }
}