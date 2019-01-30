<?php

namespace JBJ\Workflow\Event;

use JBJ\Workflow\StoreCollectionInterface;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class BackendEvent extends BaseEvent
{
    private $storeId;
    private $marking;
    private $stores;
    public function __construct(string $storeId, Marking $marking, StoreCollectionInterface $stores)
    {
        $this->storeId = $storeId;
        $this->marking = $marking;
        $this->stores = $stores;
    }
    public function getStoreId()
    {
        return $this->storeId;
    }
    public function getMarking()
    {
        return $this->marking;
    }
    public function getStores()
    {
        return $this->stores;
    }
}
