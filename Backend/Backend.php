<?php

namespace JBJ\Workflow\Backend;

use Symfony\Component\Workflow\Marking;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use JBJ\Workflow\BackendInterface;
use JBJ\Workflow\Event\BackendEvent;
use Ramsey\Uuid\Uuid;

class Backend implements BackendInterface
{
    private $backendId;
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->backendId = $this->createId();
    }

    public function getBackendId()
    {
        return $this->backendId;
    }

    public function getMarking(string $markingStoreId, string $subjectId)
    {
        $event = new BackendEvent($markingStoreId, $subjectId);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch('workflow.backend.get', $event);
    }

    public function setMarking(string $markingStoreId, string $subjectId, array $places)
    {
        BackendEvent($markingStoreId, $subjectId, $places);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch('workflow.backend.setting', $event);
        $dispatcher->dispatch('workflow.backend.set', $event);
    }

    public function createId() :string
    {
        .return Uuid::uuid4();
    }
}
