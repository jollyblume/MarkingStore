<?php

namespace JBJ\Workflow\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class WorkflowEvent extends BaseEvent
{
    private $markingStoreId;
    private $subjectId;
    private $places;

    public function __construct(string $markingStoreId, string $subjectId, array $places = [])
    {
        $this->markingStoreId = $markingStoreId;
        $this->subjectId = $subjectId;
        $this->places = $places;
    }
    public function getMarkingStoreId()
    {
        return $this->markingStoreId;
    }
    public function getSubjectId()
    {
        return $this->subjectId;
    }
    public function getPlaces()
    {
        return $this->places;
    }
    public function setPlaces(array $places)
    {
        $this->places = $places;
    }
}
