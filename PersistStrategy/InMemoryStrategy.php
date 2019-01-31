<?php

namespace JBJ\Workflow\PersistStrategy;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\PersistStrategyInterface;

class InMemoryStrategy implements PersistStrategyInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->info('InMemoryStrategy created');
    }

    public function storeCreated(string $markingStoreId, string $subjectId, array $places)
    {

    }

    public function getPlaces(string $markingStoreId, string $subjectId)
    {

    }

    public function setPlaces(string $markingStoreId, string $subjectId, array $places)
    {

    }

    public function flush()
    {

    }
}
