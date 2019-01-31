<?php

namespace JBJ\Workflow\Event;

class StoreEvent extends WorkflowEvent
{
    private $markingStore;

    public function __construct(MarkingStoreInterface $markingStore, string $subjectId, array $places = [])
    {
        parent::__construct($markingStore->getMarkingStoreId(), $subjectId, $places);
        $this->markingStore = $markingStore;
    }
    public function getMarkingStore()
    {
        return $this->markingStore;
    }
}
