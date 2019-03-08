<?php

namespace JBJ\Workflow\Tests\MarkingStore;

use JBJ\Workflow\MarkingStore\FlatMarkings;
use JBJ\Workflow\MarkingStore\Marking;
use PHPUnit\Framework\TestCase;

class FlatMarkingsTest extends TestCase
{
    public function testDefaults()
    {
        $markings = new FlatMarkings();
        $this->assertNull($markings->getMarking('store', 'property', '$subjectUuid'));
        $this->assertEquals([], $markings->getPlaces('store', 'property', '$subjectUuid'));
    }

    public function testSetPlaces()
    {
        $markings = new FlatMarkings();
        $this->assertEquals($markings, $markings->setPlaces('store', 'property', '$subjectUuid', 'mars'));
        $this->assertEquals(['mars'], $markings->getPlaces('store', 'property', '$subjectUuid'));
    }

    public function testSetMarking()
    {
        $markings = new FlatMarkings();
        $marking = new Marking('store', 'property', '$subjectUuid', 'mars');
        $this->assertEquals($markings, $markings->setMarking($marking));
        $this->assertEquals($markings, $markings->getMarking('store', 'property', '$subjectUuid'));
        $this->assertEquals(['mars'], $markings->getPlaces('store', 'property', '$subjectUuid'));
    }

    public function testGetMarkingName()
    {
        $markings = new FlatMarkings();
        $this->assertEquals('store/property/subjectUuid', $markings->getMarkingName('store', 'property', '$subjectUuid'));
    }
}
