<?php

namespace JBJ\Workflow\MarkingStore\Tests;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
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

    protected function getLogger()
    {
        $logger = new class() implements LoggerInterface {
            use LoggerTrait;
            private $level;
            private $message;
            private $context;
            public function log($level, $message, array $context = [])
            {
                $this->level = $level;
                $this->message = $message;
                $this->context = $context;
            }
            public function getLevel()
            {
                return $this->level;
            }
            public function getMessage()
            {
                return $this->msg;
            }
            public function getContext()
            {
                return $this->context;
            }
        };
        return $logger;
    }

    public function testDefaults()
    {
        $mediator = new Mediator('test.mediator');
        $this->assertEquals('test.mediator', $mediator->getName());
        $this->assertEquals('subjectUuid', $mediator->getDefaultProperty());
        $this->assertInstanceOf(PropertyAccessorInterface::class, $mediator->getPropertyAccessor());
        $this->assertNull($mediator->getDispatcher());
        $this->assertFalse($mediator->notifyCreated('test.store', 'test.property'));
        $this->assertFalse($mediator->getPlaces('test.store', 'test.subject', 'test.property'));
        $this->assertFalse($mediator->setPlaces('test.store', 'test.subject', 'test.property', ['harry', 'sally']));
    }

    public function testDefaultsWithPropertySet()
    {
        $mediator = new Mediator('test.mediator', 'test.id');
        $this->assertEquals('test.id', $mediator->getDefaultProperty());
    }

    /** @expectedException \JBJ\Workflow\Exception\InvalidArgumentException */
    public function testPropertySetToMarkingThrows()
    {
        new Mediator('test.mediator', 'marking');
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
        $this->assertTrue($mediator->notifyCreated('test.store', 'test.property'));
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
        $this->assertInternalType('array', $mediator->getPlaces('test.store', 'test.subject.uuid', 'test.property'));
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
        $this->assertTrue($mediator->setPlaces('test.store', 'test.subject.uuid', 'test.property', ['harry', 'sally']));
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
        $logger = $this->getLogger();
        $storeDispatcher = new InMemoryMarkingsListener($logger, $store);
        $dispatcher->addSubscriber($storeDispatcher);
        $mediator->setDispatcher($dispatcher);
        $this->assertTrue($mediator->notifyCreated('test.markings', 'subjectUuid'));
        $mediator->setPlaces('test.store', 'test.subject.uuid', 'test.property', ['harry', 'sally']);
        $places = $mediator->getPlaces('test.store', 'test.subject.uuid', 'test.property');
        $this->assertEquals(['harry', 'sally'], $places);
        $this->assertEquals(['test.store/test.subject.uuid/test.property' => ['harry', 'sally']], $store->getMarkings());
        $mediator->setPlaces('test.store', 'test.subject.uuid', 'test.property', []);
        $this->assertEquals([], $store->getMarkings());
        $this->assertEquals([], $mediator->getPlaces('notastore', 'notasubject', 'notaproperty'));
    }

    public function testStoreCreated()
    {
        $mediator = new Mediator('test.mediator');
        $dispatcher = new EventDispatcher();
        $subscriber = $this->getSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $mediator->setDispatcher($dispatcher);
        $this->assertTrue($mediator->notifyCreated('test.store', 'test.property'));
        $this->assertEquals($mediator, $subscriber->getEvent()->getMediator());
        $this->assertEquals('test.store', $subscriber->getEvent()->getStoreName());
        $this->assertEquals('test.property', $subscriber->getEvent()->getProperty());
    }
}
