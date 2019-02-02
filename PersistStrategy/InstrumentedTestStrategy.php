<?php

namespace JBJ\Workflow\PersistStrategy;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\PersistStrategyInterface;
use JBJ\Workflow\Document\InMemoryCollection;

class InstrumentedTestStrategy implements PersistStrategyInterface
{
    private $eventHistory = [];

    private function addEvent(string event, string $markingStoreId, string $subjectId, array $places)
    {
        $eventKey = sprintf('%s/%s', $markingStoreId, $subjectId);
        $this->EventHistory[] = [
            'event' => $event,
            'eventKey' => $eventKey,
            'places' => $places,
        ];
    }

    public function storeCreated(string $markingStoreId, string $subjectId, array $places)
    {
        $this->addEvent('workflow.store.created', $markingStoreId, $subjectId, $places);
    }

    public function getPlaces(string $markingStoreId, string $subjectId)
    {
        $this->addEvent('workflow.places.get', $markingStoreId, $subjectId, []);
    }

    public function setPlaces(string $markingStoreId, string $subjectId, array $places)
    {
        $this->addEvent('workflow.places.setting', $markingStoreId, $subjectId, $places);
    }

    public function cleanup()
    {
        $this->addEvent('workflow.places.cleanup','na', 'na', []);
    }

    public function getEventHistory()
    {
        return $this->eventHistory;
    }
}
