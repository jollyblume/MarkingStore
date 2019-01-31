<?php

namespace JBJ\Workflow\PersistStrategy;

use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Workflow\MarkingInterface;

interface PersistStrategyInterface
{
    public function storeCreated(string $markingStoreId, string $subjectId, array $places);
    public function getPlaces(string $markingStoreId, string $subjectId);
    public function setPlaces(string $markingStoreId, string $subjectId, array $places);
    public function flush();
}
