<?php

namespace JBJ\Workflow\PersistStrategy;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\PersistStrategyInterface;
use JBJ\Workflow\Document\InMemoryCollection;

class InMemoryStrategy implements PersistStrategyInterface
{
    private $logger;
    private $collection;

    public function __construct(LoggerInterface $logger, ArrayCollectionInterface $collection)
    {
        $this->logger = $logger;
        $this->collection = $collection;
        $this->logger->info('InMemoryStrategy created');
    }

    public function storeCreated(string $markingStoreId, string $subjectId, array $places)
    {}

    public function getPlaces(string $markingStoreId, string $subjectId)
    {
        $index = sprintf('%s/%s', $markingStoreId, $subjectId);
        $collection = $this->collection;
        $places = $collection[$index] ?: [];
        return $places;
    }

    public function setPlaces(string $markingStoreId, string $subjectId, array $places)
    {
        $index = sprintf('%s/%s', $markingStoreId, $subjectId);
        $collection = $this->collection;
        $collection[$index] = $places;
    }

    public function cleanup()
    {}
}
