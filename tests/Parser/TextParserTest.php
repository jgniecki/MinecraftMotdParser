<?php

namespace DevLancer\MinecraftMotdParser\Test\Parser;

use DevLancer\MinecraftMotdParser\MotdItemCollection;
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

    public function testSupportsWithValidString()
    {
        $this->assertTrue($this->parser->supports("valid string"));
    }

    public function testSupportsWithInvalidData()
    {
        $this->assertFalse($this->parser->supports(["invalid", "data"]));
    }
}