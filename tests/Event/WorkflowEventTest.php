<?php

namespace JBJ\Workflow\MarkingStore\Tests\Event;

use JBJ\Workflow\MarkingStore\Event\MarkingStoreEvent;
use JBJ\Workflow\MarkingStore\MediatorInterface;
use PHPUnit\Framework\TestCase;

class MarkingStoreEventTest extends TestCase
{
    public function testGetStoreName()
    {
        $storeName = 'test.store';
        $subjectUuid = '20188abf-b9a6-456e-bf54-a397a219b86e';
        $property = 'subjectId';
        $mediator = $this->getMockBuilder(MediatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = new MarkingStoreEvent($storeName, $subjectUuid, $property, $mediator);
        $this->assertEquals($storeName, $event->getStoreName());
    }

    public function testGetSubjectId()
    {
        $storeName = 'test.store';
        $subjectUuid = '20188abf-b9a6-456e-bf54-a397a219b86e';
        $property = 'subjectId';
        $mediator = $this->getMockBuilder(MediatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = new MarkingStoreEvent($storeName, $subjectUuid, $property, $mediator);
        $this->assertEquals($subjectUuid, $event->getSubjectUuid());
    }

    public function testGetProperty()
    {
        $storeName = 'test.store';
        $subjectUuid = '20188abf-b9a6-456e-bf54-a397a219b86e';
        $property = 'subjectId';
        $mediator = $this->getMockBuilder(MediatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = new MarkingStoreEvent($storeName, $subjectUuid, $property, $mediator);
        $this->assertEquals($property, $event->getProperty());
    }

    public function testGetMediator()
    {
        $storeName = 'test.store';
        $subjectUuid = '20188abf-b9a6-456e-bf54-a397a219b86e';
        $property = 'subjectId';
        $mediator = $this->getMockBuilder(MediatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = new MarkingStoreEvent($storeName, $subjectUuid, $property, $mediator);
        $this->assertEquals($mediator, $event->getMediator());
    }

    public function testGetPlaces()
    {
        $storeName = 'test.store';
        $subjectUuid = '20188abf-b9a6-456e-bf54-a397a219b86e';
        $property = 'subjectId';
        $mediator = $this->getMockBuilder(MediatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $expectedPlaces = [
            'this.place',
            'that.place',
            'high.place',
            'low.place',
        ];
        $event = new MarkingStoreEvent($storeName, $subjectUuid, $property, $mediator, $expectedPlaces);
        $this->assertEquals($expectedPlaces, $event->getPlaces());
    }
}
