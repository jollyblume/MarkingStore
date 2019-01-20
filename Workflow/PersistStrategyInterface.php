<?php

namespace JBJ\Workflow\Workflow;

use JBJ\Workflow\Workflow\Marking\MarkingStoreCollectionInterface as StoreCollectionInterface;

interface PersistStrategyInterface {
    public function isMigrationDisabled(StoreCollectionInterface $store);
    public function isMigrationValid(StoreCollectionInterface $store);
    public function isMigrated(StoreCollectionInterface $store);
    public function getMetadataValue(StoreCollectionInterface $store, string $key);
    public function executeMigration(StoreCollectionInterface $store);
    public function persist(StoreCollectionInterface $store, string $storeId, Marking $marking);
    public function flush();
}
