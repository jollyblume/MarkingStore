<?php

namespace JBJ\Workflow\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class BackendEvent extends BaseEvent
{
    private $storeId;
    private $marking;
    public function __construct(string $storeId, Marking $marking)
    {
        $this->storeId = $storeId;
        $this->marking = $marking;
    }
    public function getStoreId()
    {
        return $this->storeId;
    }
    public function getMarking()
    {
        return $this->marking;
    }
}
