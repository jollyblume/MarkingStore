<?php

namespace JBJ\Workflow\MarkingStore\Tests;

use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use JBJ\Workflow\MarkingStore\Mediator;
use JBJ\Workflow\MarkingStore\Event\MarkingStoreEvent;
use JBJ\Workflow\MarkingStore\EventListener\InMemoryMarkingsListener;
use JBJ\Workflow\MarkingStore\InMemoryMarkings;
use PHPUnit\Framework\TestCase;

class MediatorTest extends TestCase
{
    protected function getSubscriber()
    {
        $subscriber = new class() implements EventSubscriberInterface {
            private $event;
            public function onEvent(MarkingStoreEvent $event)
            {
                $this->event = $event;
            }
            public function getEvent()
            {
                return $this->event;
            }
            public static function getSubscribedEvents()
            {
                return [
                    'workflow.store.created' => ['onEvent'],
                    'workflow.places.get' => ['onEvent'],
                    'workflow.places.setting' => ['onEvent'],
                    'workflow.places.set' => ['onEvent'],
                ];
            }
        };
        return $subscriber;
    }

    public function testDefaults()
    {
        $mediator = new Mediator('test.mediator');
        $this->assertEquals('test.mediator', $mediator->getName());
        $this->assertEquals('subjectUuid', $mediator->getDefaultProperty());
        $this->assertNull($mediator->getDispatcher());
        $this->assertInstanceOf(PropertyAccessorInterface::class, $mediator->getPropertyAccessor());
    }

    public function testDefaultsWithProperty()
    {
        $mediator = new Mediator('test.mediator', 'test.id');
        $this->assertEquals('test.id', $mediator->getDefaultProperty());
    }

    /** @expectedException \JBJ\Workflow\Exception\InvalidArgumentException */
    public function testMarkingAsPropertyThrows()
    {
        new Mediator('test.mediator', 'marking');
    }

    /** @expectedException \JBJ\Workflow\Exception\DomainException */
    public function testNotifyCreatedThrowsIfNoDispatcher()
    {
        $mediator = new Mediator('test.mediator');
        $mediator->notifyCreated('test.store', 'test.property');
    }

    /** @expectedException \JBJ\Workflow\Exception\DomainException */
    public function tesGetPlacesThrowsIfNoDispatcher()
    {
        $mediator = new Mediator('test.mediator');
        $mediator->getPlaces('test.store', 'test.subject.uuid', 'test.property');
    }

    /** @expectedException \JBJ\Workflow\Exception\DomainException */
    public function testSetPlacesThrowsIfNoDispatcher()
    {
        $mediator = new Mediator('test.mediator');
        $mediator->setPlaces('test.store', 'test.subject.uuid', 'test.property', []);
    }

    public function testSetDispatcher()
    {
        $mediator = new Mediator('test.mediator');
        $dispatcher = new EventDispatcher();
        $mediator->setDispatcher($dispatcher);
        $this->assertEquals($dispatcher, $mediator->getDispatcher());
    }

    public function testNotifyCreated()
    {
        $mediator = new Mediator('test.mediator');
        $dispatcher = new EventDispatcher();
        $subscriber = $this->getSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $mediator->setDispatcher($dispatcher);
        $mediator->notifyCreated('test.store', 'test.property');
        $this->assertEquals($mediator, $subscriber->getEvent()->getMediator());
        $this->assertEquals('test.store', $subscriber->getEvent()->getStoreName());
        $this->assertEquals('test.property', $subscriber->getEvent()->getProperty());
    }

    public function tesGetPlaces()
    {
        $mediator = new Mediator('test.mediator');
        $dispatcher = new EventDispatcher();
        $subscriber = $this->getSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $mediator->setDispatcher($dispatcher);
        $mediator->getPlaces('test.store', 'test.subject.uuid', 'test.property');
        $this->assertEquals($mediator, $subscriber->getEvent()->getMediator());
        $this->assertEquals('test.store', $subscriber->getEvent()->getStoreName());
        $this->assertEquals('test.subject.uuid', $subscriber->getEvent()->getSubjectUuid());
        $this->assertEquals('test.property', $subscriber->getEvent()->getProperty());
        $this->assertEquals([], $subscriber->getEvent()->getPlaces());
    }

    public function testSetPlacesInitializesEvent()
    {
        $mediator = new Mediator('test.mediator');
        $dispatcher = new EventDispatcher();
        $subscriber = $this->getSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $mediator->setDispatcher($dispatcher);
        $mediator->setPlaces('test.store', 'test.subject.uuid', 'test.property', ['harry', 'sally']);
        $this->assertEquals($mediator, $subscriber->getEvent()->getMediator());
        $this->assertEquals('test.store', $subscriber->getEvent()->getStoreName());
        $this->assertEquals('test.subject.uuid', $subscriber->getEvent()->getSubjectUuid());
        $this->assertEquals('test.property', $subscriber->getEvent()->getProperty());
        $this->assertEquals(['harry', 'sally'], $subscriber->getEvent()->getPlaces());
    }

    public function testSetPlaces()
    {
        $mediator = new Mediator('test.mediator');
        $dispatcher = new EventDispatcher();
        $subscriber = $this->getSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $store = new InMemoryMarkings('test.markings');
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeDispatcher = new InMemoryMarkingsListener($logger, $store);
        $dispatcher->addSubscriber($storeDispatcher);
        $mediator->setDispatcher($dispatcher);
        $mediator->setPlaces('test.store', 'test.subject.uuid', 'test.property', ['harry', 'sally']);
        $places = $mediator->getPlaces('test.store', 'test.subject.uuid', 'test.property');
        $this->assertEquals(['harry', 'sally'], $places);
        $this->assertEquals(['test.store/test.subject.uuid/test.property' => ['harry', 'sally']], $store->getMarkings());
    }

    public function testToString()
    {
        $mediator = new Mediator('test.mediator');
        $this->assertEquals('test.mediator', strval($mediator));
    }
}
