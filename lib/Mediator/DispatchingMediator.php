<?php

namespace JBJ\Workflow\MarkingStore\Mediator;

use JBJ\Workflow\MarkingStore\MediatorInterface;
use JBJ\Workflow\MarkingStore\Event\MarkingStoreEvent;
use JBJ\Workflow\Traits\NameTrait;
use JBJ\Workflow\Traits\ParentTrait;
use JBJ\Workflow\Traits\CreateUuidTrait;
use JBJ\Workflow\Traits\EventDispatcherTrait;
use JBJ\Workflow\Traits\PropertyAccessorTrait;
use JBJ\Workflow\Exception\DomainException;
use JBJ\Workflow\Exception\InvalidArgumentException;

class DispatchingMediator implements MediatorInterface
{
    use NameTrait, ParentTrait, CreateUuidTrait, EventDispatcherTrait, PropertyAccessorTrait {
        setDispatcher as public;
        createUuid as public;
    }

    private $property;

    public function __construct(string $name, string $property = 'subjectUuid')
    {
        $this->setName($name);
        $this->property = $property;
        if ('marking' === $property) {
            throw new InvalidArgumentException('The property named "marking" is reserved for symfony/workflow');
        }
    }

    public function getDefaultProperty()
    {
        return $this->property;
    }

    public function getPropertyAccessor()
    {
        $propertyAccessor = $this->propertyAccessor ?: $this->createPropertyAccessor();
        return $propertyAccessor;
    }

    protected function sendEvent(string $eventName, string $storeName, string $subjectUuid, string $property, $places = [])
    {
        $dispatcher = $this->getDispatcher();
        if (!$dispatcher) {
            return false;
        }
        $event = new MarkingStoreEvent($storeName, $subjectUuid, $property, (array) $places);
        return $dispatcher->dispatch($eventName, $event);
    }

    public function notifyCreated(string $storeName, string $property)
    {
        $event = $this->sendEvent('workflow.store.created', $storeName, '', $property);
        return $event !== false;
    }

    public function getPlaces(string $storeName, string $subjectUuid, string $property)
    {
        $event = $this->sendEvent('workflow.places.get', $storeName, $subjectUuid, $property);
        $places = $event === false ? false : $event->getPlaces();
        return $places;
    }

    public function setPlaces(string $storeName, string $subjectUuid, string $property, $places)
    {
        $event = $this->sendEvent('workflow.places.setting', $storeName, $subjectUuid, $property, $places);
        if ($event) {
            $event = $this->sendEvent('workflow.places.set', $storeName, $subjectUuid, $property, $places);
        }
        return $event !== false;
    }
}
