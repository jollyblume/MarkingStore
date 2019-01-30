<?php

namespace JBJ\Workflow\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class BackendEvent extends BaseEvent
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
    public function getMarking(string $markingStoreId, string $subjectId)
    {
        return $this->marking;
    }
}
