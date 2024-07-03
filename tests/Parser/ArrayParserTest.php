<?php

namespace DevLancer\MinecraftMotdParser\Test\Parser;


use DevLancer\MinecraftMotdParser\Collection\MotdItemCollection;
use DevLancer\MinecraftMotdParser\Parser\ArrayParser;
use PHPUnit\Framework\TestCase;

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
            ['text' => 'World', 'color' => 'blue', 'bold' => true, 'italic' => true, 'reset' => false],
            ['text' => '!', 'bold' => true],
        ];
        $collection = new MotdItemCollection();
        $result = $this->parser->parse($data, $collection);

        $this->assertCount(3, $result);

        $item = $result->get(0);
        $this->assertEquals('Hello', $item->getText());
        $this->assertEquals('c', $item->getColor());

        $item = $result->get(1);
        $this->assertEquals('World', $item->getText());
        $this->assertEquals('9', $item->getColor());
        $this->assertTrue($item->isBold());
        $this->assertTrue($item->isItalic());
        $this->assertFalse($item->isReset());

        $item = $result->get(2);
        $this->assertEquals('!', $item->getText());
        $this->assertNull($item->getColor());
        $this->assertTrue($item->isBold());
        $this->assertFalse($item->isItalic());
        $this->assertFalse($item->isReset());
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