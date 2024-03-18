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
    public function parse(string $text): array
    {
        if ($text == "\n") {
            $container = new Container();
            $container->setText("\n");
            return [$container];
        }

        $result = [];
        $data = \explode("\n", $text);
        $elem = 1;

        foreach ($data as $item) {
            if (empty($item) && \count($data) > $elem) {
                $result = \array_merge($result, $this->parse("\n"));
                $elem++;
                continue;
            }

            \preg_match_all('/(§|\\u00A7)([0-9a-fk-or])/', $item, $match);
            $split = \preg_split('/(§|\\u00A7)([0-9a-fk-or])/', $item);
            $container = new Container();


            if (isset($split[0]) && trim($split[0]) == "") {
                unset($split[0]);
                sort($split, SORT_NUMERIC );
            }

            foreach ($split as $key => $value) {
                $container = clone $container;
                if (!isset($match[0][$key])) {
                    if (!empty(trim($value))) {
                        $container->setText($value);
                        $result[] = $container;
                    }
                    continue;
                }

                if (Format::isColor($match[0][$key])) {
                    $container->setColor(Format::getColorHex($match[0][$key]));
                } elseif (Format::isFormat($match[0][$key])) {
                    $code = $match[2][$key];
                    if (($code == "r")) {
                        $container = new Container();
                        $container->setReset(true);
                    } else {
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
                    }
                }

                if (!empty($value)) {
                    $container->setText($value);
                    $result[] = $container;
                }
            }

            if (\count($data) > $elem++) {
                $container = new Container();
                $container->setText("\n");
                $result[] = $container;
            }
        }

        return $result;
    }
}