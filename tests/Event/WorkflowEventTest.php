<?php

namespace JBJ\Workflow\MarkingStore\Tests\Event;

use JBJ\Workflow\MarkingStore\Event\MarkingStoreEvent;
use PHPUnit\Framework\TestCase;

class MarkingStoreEventTest extends TestCase
{
    public function testGetMarkingStoreId()
    {
        $event = new MarkingStoreEvent('test.marking-store-id');
        $markingStoreId = $event->getMarkingStoreId();
        $this->assertEquals('test.marking-store-id', $markingStoreId);
    }

    public function testGetSubjectId()
    {
        $event = new MarkingStoreEvent('test.marking-store-id', 'test.subject-id');
        $subjectId = $event->getSubjectId();
        $this->assertEquals('test.subject-id', $subjectId);
    }

    public function testGetPlaces()
    {
        $expectedPlaces = [
            'this.place',
            'that.place',
            'high.place',
            'low.place',
        ];
        $event = new MarkingStoreEvent('test.marking-store-id', 'test.subject-id', $expectedPlaces);
        $places = $event->getPlaces();
        $this->assertEquals($expectedPlaces, $places);
    }
}
