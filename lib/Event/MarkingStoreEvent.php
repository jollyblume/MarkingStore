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

    public function __construct(string $storeName, string $subjectUuid, string $property, $places = [])
    {
        $this->storeName = $storeName;
        $this->subjectUuid = $subjectUuid;
        $this->property = $property;
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
    public function getPlaces()
    {
        return $this->places;
    }
    public function setPlaces($places)
    {
        $this->places = (array) $places;
    }
}
