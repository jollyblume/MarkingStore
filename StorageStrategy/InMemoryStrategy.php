<?php

namespace JBJ\Workflow\StorageStrategy;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\StorageStrategyInterface;
use JBJ\Workflow\Document\InMemoryCollection;

class InMemoryStrategy implements StorageStrategyInterface
{
    private $logger;
    private $collection;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->collection = [];
        $this->logger->info('InMemoryStrategy created');
    }

    public function storeCreated(string $markingStoreId, string $subjectId, array $places)
    {}

    public function getPlaces(string $markingStoreId, string $subjectId)
    {
        $index = sprintf('%s/%s', $markingStoreId, $subjectId);
        if (array_key_exists($index, $this->collection)) {
            return $this->collection[$index] ?: [];
        }
        return [];
    }

    public function setPlaces(string $markingStoreId, string $subjectId, array $places)
    {
        $index = sprintf('%s/%s', $markingStoreId, $subjectId);
        if (empty($places)) {
            unset($this->collection[$index]);
        }
        if (!empty($places)) {
            $this->collection[$index] = $places;
        }
    }

    public function cleanup()
    {}
}
