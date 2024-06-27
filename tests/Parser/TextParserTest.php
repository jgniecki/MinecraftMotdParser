<?php

namespace DevLancer\MinecraftMotdParser\Test\Parser;

use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Parser\TextParser;
use PHPUnit\Framework\TestCase;

class TextParserTest extends TestCase
{
    private TextParser $parser;

    protected function setUp(): void
    {
        $this->parser = new TextParser();
    }

    public function testParseWithValidData()
    {
        $data = "§aHello §bWorld";
        $collection = new MotdItemCollection();
        $result = $this->parser->parse($data, $collection);

        $this->assertCount(2, $result);

        $item1 = $result->all()[0];
        $this->assertEquals("Hello ", $item1->getText());
        $this->assertEquals("a", $item1->getColor());

        $item2 = $result->all()[1];
        $this->assertEquals("World", $item2->getText());
        $this->assertEquals("b", $item2->getColor());
    }

    public function testParseWithCustomSymbol()
    {
        $data = "A &l&fMine&4craft &rServer";
        $collection = new MotdItemCollection();
        $parser = new TextParser(null, null, "&");
        $result = $parser->parse($data, $collection);

        $this->assertCount(4, $result);
        $item1 = $result->get(0);
        $this->assertEquals("A ", $item1->getText());

        $item2 = $result->get(1);
        $this->assertEquals("Mine", $item2->getText());
        $this->assertEquals("f", $item2->getColor());
        $this->assertTrue($item2->isBold());

        $item3 = $result->get(2);
        $this->assertEquals("craft ", $item3->getText());
        $this->assertEquals("4", $item3->getColor());
        $this->assertTrue($item3->isBold());

        $item4 = $result->get(3);
        $this->assertEquals("Server", $item4->getText());
        $this->assertTrue($item4->isReset());
    }

    public function testParseWithNewline()
    {
        $data = "§aHello\n§bWorld";
        $collection = new MotdItemCollection();
        $result = $this->parser->parse($data, $collection);

        $this->assertCount(3, $result);

        $item1 = $result->all()[0];
        $this->assertEquals("Hello", $item1->getText());
        $this->assertEquals("a", $item1->getColor());

        $item2 = $result->all()[1];
        $this->assertEquals("\n", $item2->getText());

        $item3 = $result->all()[2];
        $this->assertEquals("World", $item3->getText());
        $this->assertEquals("b", $item3->getColor());
    }

    public function testParseWithUnsupportedData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $data = ["invalid", "data"];
        $collection = new MotdItemCollection();
        $this->parser->parse($data, $collection);
    }

    public function testParseWithFormatReset()
    {
        $data = "§aHello §rWorld";
        $collection = new MotdItemCollection();
        $result = $this->parser->parse($data, $collection);

        $this->assertCount(2, $result);

        $item1 = $result->all()[0];
        $this->assertEquals("Hello ", $item1->getText());
        $this->assertEquals("a", $item1->getColor());

        $item2 = $result->all()[1];
        $this->assertEquals("World", $item2->getText());
        $this->assertNull($item2->getColor());
    }

    public function testParseWithOnlyFormat()
    {
        $data = "§0§0";
        $collection = new MotdItemCollection();
        $result = $this->parser->parse($data, $collection);

        $this->assertCount(0, $result);
    }

    public function testSupportsWithValidString()
    {
        $this->assertTrue($this->parser->supports("valid string"));
    }

    public function testSupportsWithInvalidData()
    {
        $this->assertFalse($this->parser->supports(["invalid", "data"]));
    }
}
