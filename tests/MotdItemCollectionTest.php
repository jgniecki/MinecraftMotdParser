<?php

namespace DevLancer\MinecraftMotdParser\Test;

use DevLancer\MinecraftMotdParser\MotdItem;
use DevLancer\MinecraftMotdParser\MotdItemCollection;
use PHPUnit\Framework\TestCase;

class MotdItemCollectionTest extends TestCase
{
    public function testCompareItemWithIdenticalItems()
    {
        $item1 = new MotdItem();
        $item1->setText("Hello");
        $item1->setColor("red");
        $item1->setBold(true);

        $item2 = new MotdItem();
        $item2->setText("Hello");
        $item2->setColor("red");
        $item2->setBold(true);

        $collection = new MotdItemCollection();
        $this->assertTrue($collection->compareItem($item1, $item2));
    }

    public function testCompareItemWithDifferentText()
    {
        $item1 = new MotdItem();
        $item1->setText("Hello");

        $item2 = new MotdItem();
        $item2->setText("World");

        $collection = new MotdItemCollection();
        $this->assertTrue($collection->compareItem($item1, $item2)); // Assuming the method ignores text unless it's "\n"
    }

    public function testCompareItemWithDifferentColor()
    {
        $item1 = new MotdItem();
        $item1->setColor("red");

        $item2 = new MotdItem();
        $item2->setColor("blue");

        $collection = new MotdItemCollection();
        $this->assertFalse($collection->compareItem($item1, $item2));
    }

    public function testCompareItemWithDifferentBold()
    {
        $item1 = new MotdItem();
        $item1->setBold(true);

        $item2 = new MotdItem();
        $item2->setBold(false);

        $collection = new MotdItemCollection();
        $this->assertFalse($collection->compareItem($item1, $item2));
    }

    public function testCompareItemWithAllDifferentProperties()
    {
        $item1 = new MotdItem();
        $item1->setText("Hello");
        $item1->setColor("red");
        $item1->setBold(true);
        $item1->setItalic(true);
        $item1->setUnderlined(true);
        $item1->setStrikethrough(true);
        $item1->setObfuscated(true);
        $item1->setReset(true);

        $item2 = new MotdItem();
        $item2->setText("World");
        $item2->setColor("blue");
        $item2->setBold(false);
        $item2->setItalic(false);
        $item2->setUnderlined(false);
        $item2->setStrikethrough(false);
        $item2->setObfuscated(false);
        $item2->setReset(false);

        $collection = new MotdItemCollection();
        $this->assertFalse($collection->compareItem($item1, $item2));
    }

    public function testMergeSimilarItems()
    {
        $item1 = new MotdItem();
        $item1->setText("Hello");
        $item1->setColor("red");

        $item2 = new MotdItem();
        $item2->setText(" World");
        $item2->setColor("red");

        $collection = new MotdItemCollection();
        $collection->add($item1);
        $collection->add($item2);

        $collection->mergeSimilarItem();

        $this->assertCount(1, $collection);
        $this->assertEquals("Hello World", $collection->all()[0]->getText());
    }

    public function testDoNotMergeDifferentItems()
    {
        $item1 = new MotdItem();
        $item1->setText("Hello");
        $item1->setColor("red");

        $item2 = new MotdItem();
        $item2->setText("World");
        $item2->setColor("blue");

        $collection = new MotdItemCollection();
        $collection->add($item1);
        $collection->add($item2);

        $collection->mergeSimilarItem();

        $this->assertCount(2, $collection);
        $this->assertEquals("Hello", $collection->all()[0]->getText());
        $this->assertEquals("World", $collection->all()[1]->getText());
    }

    public function testMergeWithNewline()
    {
        $item1 = new MotdItem();
        $item1->setText("\n");
        $item1->setColor("red");

        $item2 = new MotdItem();
        $item2->setText("World");
        $item2->setColor("red");

        $collection = new MotdItemCollection();
        $collection->add($item1);
        $collection->add($item2);

        $collection->mergeSimilarItem();

        $this->assertCount(2, $collection);
        $this->assertEquals("\n", $collection->all()[0]->getText());
        $this->assertEquals("World", $collection->all()[1]->getText());

    }

    public function testMergeMultipleItems()
    {
        $item1 = new MotdItem();
        $item1->setText("Hello");
        $item1->setColor("red");

        $item2 = new MotdItem();
        $item2->setText(" ");
        $item2->setColor("red");

        $item3 = new MotdItem();
        $item3->setText("World");
        $item3->setColor("red");

        $collection = new MotdItemCollection();
        $collection->add($item1);
        $collection->add($item2);
        $collection->add($item3);

        $collection->mergeSimilarItem();

        $this->assertCount(1, $collection);
        $this->assertEquals("Hello World", $collection->all()[0]->getText());
    }
}
