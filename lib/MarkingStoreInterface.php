<?php

namespace JBJ\Workflow\MarkingStore;

interface MarkingStoreInterface
{
    /**
     * @return string $storeId
     */
    public function getMarkingStoreId();

    // public function getStores();
    // public function setStores(?StoreCollectionInterface $stores);
}
