<?php

namespace JBJ\Workflow\Event;

class StoreEvent extends WorkflowEvent
{
    private $markingStore;

    public function __construct(string $markingStoreId, string $subjectId, array $places = [])
    {
        $this->markingStore = $markingStore;
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
