<?php

namespace DevLancer\MinecraftMotdParser\Test\Parser;


use PHPUnit\Framework\TestCase;
use DevLancer\MinecraftMotdParser\Parser\ArrayParser;
use DevLancer\MinecraftMotdParser\MotdItemCollection;
use DevLancer\MinecraftMotdParser\MotdItem;

class ArrayParserTest extends TestCase
{
    private ArrayParser $parser;

    protected function setUp(): void
    {
        $this->parser = new ArrayParser();
    }

    public function testParseWithValidData()
    {
        $data = [
            ['text' => 'Hello', 'color' => 'red'],
            ['text' => 'World', 'color' => 'blue']
        ];
        $collection = new MotdItemCollection();
        $result = $this->parser->parse($data, $collection);

        $this->assertCount(2, $result);

        $item1 = $result->all()[0];
        $this->assertEquals('Hello', $item1->getText());
        $this->assertEquals('c', $item1->getColor());

        $item2 = $result->all()[1];
        $this->assertEquals('World', $item2->getText());
        $this->assertEquals('9', $item2->getColor());
    }

    public function testParseWithInvalidData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $data = "invalid data";
        $collection = new MotdItemCollection();
        $this->parser->parse($data, $collection);
    }

    public function testSupportsWithValidArray()
    {
        $data = [
            ['text' => 'Hello']
        ];
        $this->assertTrue($this->parser->supports($data));
    }

    public function testSupportsWithInvalidData()
    {
        $data = "invalid data";
        $this->assertFalse($this->parser->supports($data));
    }
}