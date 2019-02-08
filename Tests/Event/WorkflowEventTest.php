<?php

namespace JBJ\Workflow\MarkingStore\Tests\Event;

use JBJ\Workflow\MarkingStore\Event\WorkflowEvent;
use PHPUnit\Framework\TestCase;

class WorkflowEventTest extends TestCase
{
    public function testGetMarkingStoreId() {
        $event = new WorkflowEvent('test.marking-store-id');
        $markingStoreId = $event->getMarkingStoreId();
        $this->assertEquals('test.marking-store-id', $markingStoreId);
    }

    public function testGetSubjectId() {
        $event = new WorkflowEvent('test.marking-store-id', 'test.subject-id');
        $subjectId = $event->getSubjectId();
        $this->assertEquals('test.subject-id', $subjectId);
    }

    public function testGetPlaces() {
        $expectedPlaces = [
            'this.place',
            'that.place',
            'high.place',
            'low.place',
        ];
        $event = new WorkflowEvent('test.marking-store-id', 'test.subject-id', $expectedPlaces);
        $places = $event->getPlaces();
        $this->assertEquals($expectedPlaces, $places);
    }
}
