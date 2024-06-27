<?php

namespace DevLancer\MinecraftMotdParser\Test\Generator;

use DevLancer\MinecraftMotdParser\Generator\HtmlGenerator;
use DevLancer\MinecraftMotdParser\MotdItem;
use DevLancer\MinecraftMotdParser\MotdItemCollection;
use PHPUnit\Framework\TestCase;

class HtmlGeneratorTest extends TestCase
{
    public function testGenerateWithSingleItem()
    {
        $collection = new MotdItemCollection();
        $item = new MotdItem();
        $item->setText('Hello');
        $item->setColor('c');
        $collection->add($item);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="color: #FF5555;">Hello</span>', $result);
    }

    public function testGenerateWithMultipleItems()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText('Hello');
        $item1->setColor('c');
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText('World');
        $item2->setColor('a');
        $collection->add($item2);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="color: #FF5555;">Hello</span><span style="color: #55FF55;">World</span>', $result);
    }

    public function testGenerateWithReset()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText('Hello');
        $item1->setColor('c');
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText('World');
        $item2->setColor('a');
        $item2->setReset(true);
        $collection->add($item2);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="color: #FF5555;">Hello</span><span style="color: #55FF55;">World</span>', $result);
    }

    public function testGenerateWithBoldFormat()
    {
        $collection = new MotdItemCollection();

        $item = new MotdItem();
        $item->setText('Bold Text');
        $item->setBold(true);
        $collection->add($item);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="font-weight: bold;">Bold Text</span>', $result);
    }

    public function testGenerateWithItalicFormat()
    {
        $collection = new MotdItemCollection();

        $item = new MotdItem();
        $item->setText('Italic Text');
        $item->setItalic(true);
        $collection->add($item);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="font-style: italic;">Italic Text</span>', $result);
    }

    public function testGenerateWithBoldAndItalicFormat()
    {
        $collection = new MotdItemCollection();

        $item = new MotdItem();
        $item->setText('Bold Italic Text');
        $item->setBold(true);
        $item->setItalic(true);
        $collection->add($item);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="font-weight: bold; font-style: italic;">Bold Italic Text</span>', $result);
    }

    public function testGenerateWithUnderlineFormat()
    {
        $collection = new MotdItemCollection();

        $item = new MotdItem();
        $item->setText('Underline Text');
        $item->setUnderlined(true);
        $collection->add($item);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="text-decoration: underline;">Underline Text</span>', $result);
    }

    public function testGenerateWithAllFormats()
    {
        $collection = new MotdItemCollection();

        $item = new MotdItem();
        $item->setText('All Formats Text');
        $item->setColor('a');
        $item->setBold(true);
        $item->setItalic(true);
        $item->setUnderlined(true);
        $item->setObfuscated(true);
        $collection->add($item);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="color: #55FF55; font-weight: bold; font-style: italic; text-decoration: underline;">All Formats Text</span>', $result);
    }

    public function testGenerateWithThreeItems()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText('Hello');
        $item1->setColor('c');
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText('Beautiful');
        $item2->setColor('e');
        $collection->add($item2);

        $item3 = new MotdItem();
        $item3->setText('World');
        $item3->setColor('a');
        $collection->add($item3);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="color: #FF5555;">Hello</span><span style="color: #FFFF55;">Beautiful</span><span style="color: #55FF55;">World</span>', $result);
    }

    public function testGenerateWithThreeItemsAndFormats()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText('Hello');
        $item1->setColor('c');
        $item1->setBold(true);
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText('Beautiful');
        $item2->setColor('e');
        $item2->setItalic(true);
        $collection->add($item2);

        $item3 = new MotdItem();
        $item3->setText('World');
        $item3->setColor('a');
        $item3->setUnderlined(true);
        $collection->add($item3);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="color: #FF5555; font-weight: bold;">Hello</span><span style="color: #FFFF55; font-style: italic;">Beautiful</span><span style="color: #55FF55; text-decoration: underline;">World</span>', $result);
    }

    public function testGenerateWithLineBreaks()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText("Hello");
        $item1->setColor('c');
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText("\n");
        $collection->add($item2);

        $item3 = new MotdItem();
        $item3->setText("Beautiful");
        $item3->setColor('e');
        $collection->add($item3);

        $item4 = new MotdItem();
        $item4->setText("\n");
        $collection->add($item4);

        $item5 = new MotdItem();
        $item5->setText("World");
        $item5->setColor('a');
        $collection->add($item5);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="color: #FF5555;">Hello</span><br /><span style="color: #FFFF55;">Beautiful</span><br /><span style="color: #55FF55;">World</span>', $result);
    }

    public function testGenerateWithMixedFormatsAndLineBreaks()
    {
        $collection = new MotdItemCollection();

        $item1 = new MotdItem();
        $item1->setText("Hello");
        $item1->setColor('c');
        $item1->setBold(true);
        $collection->add($item1);

        $item2 = new MotdItem();
        $item2->setText("\n");
        $collection->add($item2);

        $item3 = new MotdItem();
        $item3->setText("Beautiful");
        $item3->setColor('e');
        $item3->setItalic(true);
        $collection->add($item3);

        $item4 = new MotdItem();
        $item4->setText("\n");
        $collection->add($item4);

        $item5 = new MotdItem();
        $item5->setText("World");
        $item5->setColor('a');
        $item5->setUnderlined(true);
        $collection->add($item5);

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('<span style="color: #FF5555; font-weight: bold;">Hello</span><br /><span style="color: #FFFF55; font-style: italic;">Beautiful</span><br /><span style="color: #55FF55; text-decoration: underline;">World</span>', $result);
    }

    public function testGenerateWithEmptyCollection()
    {
        $collection = new MotdItemCollection();

        $generator = new HtmlGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('', $result);
    }
}