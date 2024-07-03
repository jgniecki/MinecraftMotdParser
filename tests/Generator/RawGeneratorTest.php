<?php

namespace DevLancer\MinecraftMotdParser\Test\Generator;

use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Generator\RawGenerator;
use DevLancer\MinecraftMotdParser\MotdItem;
use PHPUnit\Framework\TestCase;

class RawGeneratorTest extends TestCase
{
    private RawGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new RawGenerator();
    }

    public function testGenerate()
    {
        $motdItem = new MotdItem();
        $motdItem->setText('Hello');

        $collection = new MotdItemCollection();
        $collection->add($motdItem);

        $result = $this->generator->generate($collection);
        $this->assertEquals('Hello', $result);
    }

    public function testGenerateWithCustomSymbol()
    {

        $motdItem = new MotdItem();
        $motdItem->setText('World');
        $motdItem->setReset(true);
        $motdItem->setColor('a');

        $collection = new MotdItemCollection();
        $collection->add($motdItem);

        $generator = new RawGenerator(null, null, '$');
        $result = $generator->generate($collection);

        $this->assertEquals('$r$aWorld', $result);
    }

    public function testGenerateWithNewLine()
    {
        $motdItem1 = new MotdItem();
        $motdItem1->setText('Hello ');
        $motdItem1->setColor('c');
        $motdItem1->setBold(true);

        $motdItem2 = new MotdItem();
        $motdItem2->setText("\n");

        $motdItem3 = new MotdItem();
        $motdItem3->setText('World');
        $motdItem3->setColor('a');

        $collection = new MotdItemCollection();
        $collection->add($motdItem1);
        $collection->add($motdItem2);
        $collection->add($motdItem3);

        $result = $this->generator->generate($collection);

        $this->assertEquals("§c§lHello \n§aWorld", $result);
    }

    public function testGenerateWithReset()
    {
        $motdItem1 = new MotdItem();
        $motdItem1->setText('Hello');
        $motdItem1->setColor('c');

        $motdItem2 = new MotdItem();
        $motdItem2->setText('World');
        $motdItem2->setReset(true);
        $motdItem2->setColor('a');

        $collection = new MotdItemCollection();
        $collection->add($motdItem1);
        $collection->add($motdItem2);

        $result = $this->generator->generate($collection);

        $this->assertEquals('§cHello§r§aWorld', $result);
    }

    public function testGenerateWithFormatConflict()
    {
        $motdItem1 = new MotdItem();
        $motdItem1->setText('Hello');
        $motdItem1->setColor('c');
        $motdItem1->setBold(true);

        $motdItem2 = new MotdItem();
        $motdItem2->setText('World');
        $motdItem2->setColor('c');
        $motdItem2->setBold(false);

        $collection = new MotdItemCollection();
        $collection->add($motdItem1);
        $collection->add($motdItem2);

        $generator = new RawGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('§c§lHello§r§cWorld', $result);
    }

    public function testGenerateWithEmptyCollection()
    {
        $collection = new MotdItemCollection();

        $generator = new RawGenerator();
        $result = $generator->generate($collection);

        $this->assertEquals('', $result);
    }
}