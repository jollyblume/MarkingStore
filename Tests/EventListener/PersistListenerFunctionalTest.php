<?php

namespace JBJ\Workflow\Tests\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use JBJ\Workflow\Event\WorkflowEvent;
use JBJ\Workflow\EventListener\PersistListener;
use JBJ\Workflow\PersistStrategy\InMemoryStrategy;
use PHPUnit\Framework\TestCase;

class PersistListenerFunctionalTest extends TestCase
{
    public function testGetDispatcher()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $strategy = new InMemoryStrategy($logger);
        $subscriber = new PersistListener($logger, $strategy);
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $listeners = $dispatcher->getListeners();
        foreach (['store.created', 'places.get', 'places.setting', 'places.set'] as $name) {
            $eventName = sprintf('workflow.%s', $name);
            $this->assertArrayHasKey($eventName, $listeners);
        }
        return $dispatcher;
    }

    /**
     * @depends testGetDispatcher
     */
    public function testOnStoreCreated($dispatcher)
    {
        $this->markTestSkipped('Base strategy does not implement this event handler');
    }

    /**
     * @depends testGetDispatcher
     */
    public function testOnSet($dispatcher)
    {
        $this->markTestSkipped('Base strategy does not implement this event handler');
    }

    /**
     * @depends testGetDispatcher
     */
    public function testOnGetReturnsEmptyArrayIfKeyMissing($dispatcher)
    {
        $event = new WorkflowEvent('store1', 'subject1');
        $dispatcher->dispatch('workflow.places.get', $event);
        $places = $event->getPlaces();
        $this->assertEquals([], $places);
    }
    /**
     * @depends testGetDispatcher
     */
    public function testOnSetPersistsPlaces($dispatcher)
    {
        $expectedPlaces =[
            'place1',
            'place2',
            'place3',
        ];
        $event = new WorkflowEvent('store1', 'subject1', $expectedPlaces);
        $dispatcher->dispatch('workflow.places.setting', $event);
        $event->setPlaces([]);
        $dispatcher->dispatch('workflow.places.get', $event);
        $places = $event->getPlaces();
        $this->assertEquals($expectedPlaces, $places);
    }
}
