<?php

namespace JBJ\Workflow\Workflow\Marking;

use JBJ\Workflow\Event\Workflow\BackendEvent as Event;
use JBJ\Workflow\Event\Workflow\BackendPersistEvent as PersistEvent;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\Uuid;

/**
 * MultiTenantMarkingStoreBackend
 *
 * MultiTenantMarkingStoreBackend persists the markings for multiple workflows and
 * workflow subjects (tokens).
 */
class MultiTenantMarkingStoreBackend implements MultiTenantMarkingStoreBackendInterface {
    const MARKING_STORE_COLLECTION_NAME = 'workflow.marking-store-collection';
    const MARKING_STORE_NAME = 'workflow.marking-store';

    /**
     * @var string $backendId
     */
    private $backendId;

    /**
     * @var MarkingStoreCollection $markingStoreCollection
     */
    private $markingStoreCollection;

    /**
     * @var EventDispatcherInterface $dispatcher
     */
    private $dispatcher;

    public function __construct(MarkingStoreCollection $markingStoreCollection = null, EventDispatcherInterface $dispatcher = null) {
        $this->backendId = $this->createId('workflow.backend');
        if (!$markingStoreCollection) {
            $markingStoreCollectionId = $this->createId(self::MARKING_STORE_COLLECTION_NAME);
            $markingStoreCollection = new MarkingStoreCollection($markingStoreCollectionId);
        }
        $this->markingStoreCollection = $markingStoreCollection;
        $this->dispatcher = $dispatcher;
    }

    protected function getMarkingStoreCollection() {
        return $this->markingStoreCollection;
    }

    /**
     * Get the backendId
     *
     * @return string backendId
     */
    public function getBackendId() {
        return $this->backendId;
    }

    /**
     * Get a workflow marking from the backend
     *
     * @param string $markingStoreId
     * @param string $markingId
     * @return Marking The workflow marking
     */
    public function getMarking(string $markingStoreId, string $markingId) {
        $stores = $this->getMarkingStoreCollection();
        $store = $stores[$markingStoreId] ?? null;
        if (!$store) {
            return null;
        }

        $marking = $store[$markingId] ?? null;
        return $marking;
    }

    /**
     * Persist a workflow marking to the backend
     *
     * @param string $markingStoreId
     * @param string $markingId
     * @param Marking $marking The workflow marking
     * @return self
     */
    public function setMarking(string $markingStoreId, Marking $marking) {
        $stores = $this->getMarkingStoreCollection();
        $this->settingMark($markingStoreId, $marking, $stores);
        $store = $stores[$markingStoreId] ?? null;
        if (!$store) {
            $store = new MarkingCollection($markingStoreId);
            $stores[] = $store;
            $this->newStore($markingStoreId, $marking, $stores);
        }
        $store[] = $marking;
        $this->setMark($markingStoreId, $marking, $stores);
        return $this;
    }

    /**
     * Create a new Marking id
     *
     * The markingId can be any string that is unique within a workflow domain,
     * but is normally a UUID.
     *
     * Some important workflow domains:
     *  - subject id
     *  - workflow/marking-store id
     *  - workflow/marking-store collection id
     *  - workflow definition
     *
     * @param string $name Name used in UUID5 generation
     * @return string UUID string
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function createId(string $name = 'workflow.general') :string {
        return Uuid::uuid3(Uuid::NAMESPACE_DNS, $name);
    }

    protected function createBackendEvent(string $markingStoreId, Marking $marking, MarkingStoreCollection $store) {
        $event = new BackendEvent($markingStoreId, $marking, $stores);
        return $event;
    }

    protected function createPersistEvent(string $markingStoreId, Marking $marking, MarkingStoreCollection $store) {
        $event = new PersistEvent($markingStoreId, $marking, $stores);
        return $event;
    }

    protected function dispatchBackendEvent($names, BackendEvent $event) {
        $dispatcher = $this->dispatcher;
        if (!$dispatcher) {
            return;
        }
        if (is_string($names)) {
            $names = [$names];
        }
        if (!is_array($names)) {
            throw new \Exception('$names must be string or array');
        }
        foreach ($names as $name) {
            $fqName = sprintf('backend.%s', $name);
            $dispatcher->dispatch($fqName, $event);
        }
    }

    protected function settingMark(string $markingStoreId, Marking $marking, MarkingCollection $stores = null) {
        $event = $this->createBackendEvent($markingStoreId, $marking, $stores);
        $this->dispatchBackendEvent('mark.setting', $event);
        return $this;
    }

    protected function newStore(string $markingStoreId, Marking $marking, MarkingCollection $stores = null) {
        $event = $this->createBackendEvent($markingStoreId, $marking, $stores);
        $this->dispatchBackendEvent('mark.newstore', $event);
        return $this;
    }

    protected function markingSet(string $markingStoreId, Marking $marking, MarkingCollection $stores = null) {
        $event = $this->createPersistEvent($markingStoreId, $marking, $stores);
        $this->dispatchBackendEvent('mark.persist', $event);
        if ($stores !== $event->getStores()) {
            $stores = $event->getStores();
            $this->stores = $stores;
        }
        $event = $this->createBackendEvent($markingStoreId, $marking, $stores);
        $this->dispatchBackendEvent('mark.set', $event);
        return $this;
    }
}
