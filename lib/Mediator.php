<?php

namespace JBJ\Workflow\MarkingStore;

use JBJ\Workflow\MarkingStore\Event\MarkingStoreEvent;
use JBJ\Workflow\Traits\NameTrait;
use JBJ\Workflow\Traits\ParentTrait;
use JBJ\Workflow\Traits\CreateIdTrait;
use JBJ\Workflow\Traits\EventDispatcherTrait;
use JBJ\Workflow\Traits\PropertyAccessorTrait;
use JBJ\Workflow\Exception\DomainException;

class Mediator implements MediatorInterface
{
    use NameTrait, ParentTrait, CreateIdTrait, EventDispatcherTrait, PropertyAccessorTrait;

    private $property;

    public function __construct(string $name, string $property = 'subjectId')
    {
        $this->setName($name);
        $this->property = $property;
        if ('marking' === $property) {
            throw new \JBJ\Workflow\Exception\FixMeException('The property named "marking" is reserved for symfony/workflow');
        }
    }

    protected function sendEvent(string $eventName, string $storeName, string $subjectUuid, string $property, $places = [])
    {
        $dispatcher = $this->dispatcher;
        if (!$dispatcher) {
            throw new DomainException('No event dispatcher configured');
        }
        $event = new MarkingStoreEvent($storeName, $subjectUuid, $property, $this, (array) $places);
        $dispatcher->dispatch($eventName, $event);
        return $event;
    }

    public function notifyCreated(string $storeName, string $property)
    {
        $this->sendEvent('workflow.store.created', $storeName, '', $property);
    }

    public function getPlaces(string $storeName, string $subjectUuid, string $property)
    {
        $event = $this->sendEvent('workflow.places.get', $storeName, $subjectUuid, $property);
        $places = $event->getPlaces();
        return $places;
    }

    public function setPlaces(string $storeName, string $subjectUuid, string $property, $places)
    {
        $event = $this->sendEvent('workflow.places.setting', $storeName, $subjectUuid, $property, $places);
        $event = $this->sendEvent('workflow.places.set', $storeName, $subjectUuid, $property, $places);
    }

    public function getPropertyAccessor()
    {
        $propertyAccessor = $this->propertyAccessor ?: $this->createPropertyAccessor();
        return $propertyAccessor;
    }

    public function getDefaultProperty()
    {
        return $this->property;
    }

    public function createUuid(string $name = '')
    {
        return $this->createId($name);
    }
}
