<?php

namespace DevLancer\MinecraftMotdParser\Test\Generator;

use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Generator\TextGenerator;
use DevLancer\MinecraftMotdParser\MotdItem;
use PHPUnit\Framework\TestCase;

class TextGeneratorTest extends TestCase
{
    public function testGenerateWithSingleItem()
    {
        $collection = new MotdItemCollection();
        $item = new MotdItem();
        $item->setText('Hello');
        $collection->add($item);

        $generator = new TextGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('Hello', $result);
    }

    public function testGenerateWithMultipleItems()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText('Hello ');
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText('World');
        $collection->add($item2);

        $generator = new TextGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('Hello World', $result);
    }

    public function testGenerateWithEmptyCollection()
    {
        $collection = new MotdItemCollection();

        $generator = new TextGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('', $result);
    }

    public function testGenerateWithMultipleItemsAndFormats()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText('Bold ');
        $item1->setBold(true);
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText('Italic ');
        $item2->setItalic(true);
        $collection->add($item2);

        $item3 = new MotdItem();
        $item3->setText('Underline');
        $item3->setUnderlined(true);
        $collection->add($item3);

        $generator = new TextGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('Bold Italic Underline', $result);
    }

    public function testGenerateWithLineBreaks()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText("Hello\n");
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText("World\n");
        $collection->add($item2);

        $generator = new TextGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals("Hello\nWorld\n", $result);
    }

    public function testGenerateWithMixedFormatsAndLineBreaks()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText("Hello");
        $item1->setBold(true);
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText("\n");
        $item2->setItalic(true);
        $collection->add($item2);

        $item3 = new MotdItem();
        $item3->setText("World");
        $item3->setUnderlined(true);
        $collection->add($item3);

        $generator = new TextGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals("Hello\nWorld", $result);
    }
}
