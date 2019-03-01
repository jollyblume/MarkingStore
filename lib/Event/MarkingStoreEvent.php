<?php

namespace JBJ\Workflow\MarkingStore\Event;

use JBJ\Workflow\Event\WorkflowEvent;
use JBJ\Workflow\MarkingStore\MediatorInterface;

class MarkingStoreEvent extends WorkflowEvent
{
    private $storeName;
    private $subjectUuid;
    private $property;
    private $places;
    private $mediator;

    public function __construct(string $storeName, string $subjectUuid, string $property, MediatorInterface $mediator, $places = [])
    {
        $this->storeName = $storeName;
        $this->subjectUuid = $subjectUuid;
        $this->property = $property;
        $this->mediator = $mediator;
        $this->places = (array) $places;
    }
    public function getStoreName()
    {
        return $this->storeName;
    }
    public function getSubjectUuid()
    {
        return $this->subjectUuid;
    }
    public function getProperty()
    {
        return $this->property;
    }
    public function getMediator()
    {
        return $this->mediator;
    }
    public function getPlaces()
    {
        return $this->places;
    }
    public function setPlaces($places)
    {
        $this->places = (array) $places;
    }
}
