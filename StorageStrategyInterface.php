<?php

namespace JBJ\Workflow\MarkingStore;

use Closure;

interface StorageStrategyInterface
{
    public function storeCreated(string $markingStoreId, string $subjectId, array $places);
    public function getPlaces(string $markingStoreId, string $subjectId);
    public function setPlaces(string $markingStoreId, string $subjectId, array $places);
    public function cleanup();
    public function setIndexTransformer(Closure $transformer);
}
