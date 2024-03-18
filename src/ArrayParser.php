<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki <kubuspl@onet.eu>
 * @copyright
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevLancer\MinecraftMotdParser;

class ArrayParser
{
    private array $list = [];

    public function parse(array $list): array
    {
        $this->list = [];
        $this->arrayParse([$list], new Container());

        return $this->list;
    }

    private function arrayParse(array $list, Container $parent)
    {
        foreach ($list as $data) {
            $container = clone $parent;

            if (isset($data['color']))
                $container->setColor($data['color']);

            if (isset($data['bold']))
                $container->setBold((bool) $data['bold']);

            if (isset($data['underlined']))
                $container->setUnderlined((bool) $data['underlined']);

            if (isset($data['strikethrough']))
                $container->setStrikethrough((bool) $data['strikethrough']);

            if (isset($data['italic']))
                $container->setItalic((bool) $data['italic']);

            if (isset($data['text'])) {
                $text = $data['text'];
                $newLine = strpos($text, "\n");
                $container->setText(($newLine === false)? $text : substr($text, 0, $newLine));
            } else {
                $container->setText(null);
            }

            $this->list[] = $container;
            if (isset($newLine) && $newLine !== false) {
                $container = new Container();
                $container->setText("\n");
                $this->list[] = $container;

                if (strlen(substr($text, $newLine+1)) > 0)
                    $this->arrayParse([['text' => substr($text, $newLine+1)]], new Container());

                $container = new Container();
            }

            if (isset($data['extra']))
                $this->arrayParse($data['extra'], $container);
        }
    }
    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->list;
    }
}