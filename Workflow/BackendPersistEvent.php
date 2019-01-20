<?php

namespace JBJ\Workflow\Workflow;

use JBJ\Workflow\Workflow\Marking\MarkingStoreCollectionInterface;

class BackendPersistEvent extends BackendEvent {
    public function setStores(MarkingStoreCollectionInterface $stores) {
        $this->stores = $stores;
    }
}
