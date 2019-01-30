<?php

namespace JBJ\Workflow\Tests\Traits;

use PHPUnit\Framework\TestCase;
use JBJ\Workflow\Traits\MarkingConverterTrait;

class MarkingConverterTraitTest extends TestCase
{
    public function testConvertArrayPlaces()
    {
        $mock = $this->getMockForTrait(MarkingConverterTrait::class);
        $places = ['testPlace'];
        $actualPlaces = $mock->convertPlacesToKeys($places);
        $expectedPlaces = ['testPlace' => 1];
        $this->assertEquals($expectedPlaces, $actualPlaces);
    }

    public function testConvertSymfonyPlaces()
    {
        $mock = $this->getMockForTrait(MarkingConverterTrait::class);
        $places = ['testPlace' => 1];
        $actualPlaces = $mock->convertPlacesToKeys($places);
        $expectedPlaces = ['testPlace' => 1];
        $this->assertEquals($expectedPlaces, $actualPlaces);
    }
}
