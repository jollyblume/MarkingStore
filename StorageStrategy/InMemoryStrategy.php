<?php

namespace JBJ\Workflow\MarkingStore\StorageStrategy;

use Closure;
use Psr\Log\LoggerInterface;
use JBJ\Workflow\MarkingStore\StorageStrategyInterface;
use JBJ\Workflow\MarkingStore\Document\InMemoryCollection;

class InMemoryStrategy implements StorageStrategyInterface
{
    private $logger;
    private $collection;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->collection = [];
        $this->logger->info('InMemoryStrategy created');
        $this->setDefaultIndexTransformer();
    }

    public function storeCreated(string $markingStoreId, string $subjectId, array $places)
    {
    }

    protected function setDefaultIndexTransformer()
    {
        $transformer = function (string $markingStoreId, string $subjectId) {
            $index = sprintf('%s/%s', $markingStoreId, $subjectId);
            return $index;
        };
        $this->setIndexTransformer($transformer);
    }

    private $transformer;
    public function setIndexTransformer(Closure $transformer)
    {
        $this->transformer = $transformer;
    }

    protected function createIndex(string $markingStoreId, string $subjectId)
    {
        $transformer = $this->transformer;
        $index = strval($transformer($markingStoreId, $subjectId));
        return $index;
    }

    public function getPlaces(string $markingStoreId, string $subjectId)
    {
        $index = $this->createIndex($markingStoreId, $subjectId);
        if (array_key_exists($index, $this->collection)) {
            return $this->collection[$index] ?: [];
        }
        return [];
    }

    public function setPlaces(string $markingStoreId, string $subjectId, array $places)
    {
        $index = $this->createIndex($markingStoreId, $subjectId);
        if (empty($places)) {
            unset($this->collection[$index]);
        }
        if (!empty($places)) {
            $this->collection[$index] = $places;
        }
    }

    public function cleanup()
    {
    }
}
