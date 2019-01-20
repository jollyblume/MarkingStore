<?php

namespace JBJ\Workflow\Event;

use JBJ\Workflow\StoreCollectionInterface;

class BackendPersistEvent extends BackendEvent {
    public function setStores(StoreCollectionInterface $stores) {
        $this->stores = $stores;
    }
}
