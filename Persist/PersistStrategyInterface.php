<?php

namespace JBJ\Workflow\Persist;

use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Workflow\MarkingInterface;

interface PersistStrategyInterface
{
    public function isMigrationDisabled(StoreCollectionInterface $stores);
    public function isMigrationValid(StoreCollectionInterface $stores);
    public function isMigrated(StoreCollectionInterface $stores);
    public function getMetadataValue(StoreCollectionInterface $stores, string $key);
    public function executeMigration(StoreCollectionInterface $stores);
    public function persist(StoreCollectionInterface $stores, string $storeId, MarkingInterface $marking);
    public function flush();
}
