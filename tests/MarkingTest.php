<?php

namespace JBJ\Workflow\Tests\MarkingStore;

use JBJ\Workflow\MarkingStore\Marking;
use PHPUnit\Framework\TestCase;

class MarkingTest extends TestCase
{
    public function testDefaults()
    {
        $marking = new Marking('store', 'property');
        $this->assertEquals('store', $marking->getStoreName());
        $this->assertEquals('property', $marking->getProperty());
        $this->assertEquals([], $marking->getPlaces());
        $this->assertEquals('store/property', $marking->getName());
        $this->assertEquals('store/property', strval($marking));
    }

    public function testDefaultsWithSubjectUuidProvided()
    {
        $marking = new Marking('store', 'property', 'subjectUuid');
        $this->assertEquals('subjectUuid', $marking->getSubjectUuid());
        $this->assertEquals('store/property/subjectUuid', $marking->getName());
        $this->assertEquals('store/property/subjectUuid', strval($marking));
    }

    public function testDefaultsWithVariodicPlacesProvided()
    {
        $marking = new Marking('store', 'property', 'subjectUuid', 'here', 'there');
        $this->assertEquals(['here', 'there'], $marking->getPlaces());
    }

    public function testDefaultsWithUnpackedPlacesProvided()
    {
        $marking = new Marking('store', 'property', 'subjectUuid', ...['here', 'there']);
        $this->assertEquals(['here', 'there'], $marking->getPlaces());
    }

    public function testCreateFrom()
    {
        $marking = new Marking('store', 'property', 'subjectUuid', ...['here', 'there']);
        $newMarking = $marking->createFrom('everywhere');
        $this->assertEquals('store', $newMarking->getStoreName());
        $this->assertEquals('property', $newMarking->getProperty());
        $this->assertEquals(['everywhere'], $newMarking->getPlaces());
        $this->assertEquals('store/property/subjectUuid', $marking->getName());
    }
}
