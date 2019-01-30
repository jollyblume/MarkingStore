<?php

namespace JBJ\Workflow\Workflow\Marking;

use JBJ\Workflow\Event\BackendEvent as Event;
use JBJ\Workflow\Event\BackendPersistEvent as PersistEvent;
use JBJ\Workflow\MarkingInterface;
use JBJ\Workflow\Document\Marking;
use JBJ\Workflow\MarkingStoreInterface;
use JBJ\Workflow\Document\MarkingStore;
use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Workflow\Document\StoreCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\Uuid;

/**
 * MultiTenantMarkingStoreBackend
 *
 * MultiTenantMarkingStoreBackend persists the markings for multiple workflows and
 * workflow subjects (tokens).
 */
class ShimmedBackend implements ShimmedBackendInterface
{
    const STORE_COLLECTION_NAME = 'workflow.marking-store-collection';
    const MARKING_STORE_NAME = 'workflow.marking-store';

    /**
     * @var string $backendId
     */
    private $backendId;

    /**
     * @var StoreCollection $stores
     */
    private $stores;

    /**
     * @var EventDispatcherInterface $dispatcher
     */
    private $dispatcher;

    public function __construct(StoreCollectionInterface $stores = null, EventDispatcherInterface $dispatcher = null, string $backendId = '')
    {
        if (empty($backendId)) {
            $backendId = $this->createId('workflow.backend');
        }
        $this->backendId = $backendId;
        if (!$stores) {
            $storesId = $this->createId(self::STORE_COLLECTION_NAME);
            $stores = new StoreCollection($backendId);
        }
        $this->stores = $stores;
        $this->dispatcher = $dispatcher;
    }

    protected function getStoreCollection()
    {
        return $this->$stores;
    }

    /**
     * Get the backendId
     *
     * @return string backendId
     */
    public function getBackendId()
    {
        return $this->backendId;
    }

    /**
     * Get a workflow marking from the backend
     *
     * @param string $storeId
     * @param string $markingId
     * @return Marking The workflow marking
     */
    public function getMarking(string $storeId, string $markingId)
    {
        $stores = $this->getStoreCollection();
        $store = $stores[$storeId] ?? null;
        if (!$store) {
            return null;
        }

        $marking = $store[$markingId] ?? null;
        return $marking;
    }

    /**
     * Persist a workflow marking to the backend
     *
     * @param string $storeId
     * @param Marking $marking The workflow marking
     * @return self
     */
    public function setMarking(string $storeId, MarkingInterface $marking)
    {
        $stores = $this->getStoreCollection();
        $this->settingMark($storeId, $marking, $stores); // multiple events
        $store = $stores[$storeId] ?? null;
        if (!$store) {
            $store = new MarkingStore($storeId);
            $stores[] = $store;
            $this->newStore($storeId, $marking, $stores); // event
        }
        $store[] = $marking;
        $this->setMark($storeId, $marking, $stores); // multiple events
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
    public function createId(string $name = 'workflow.general') :string
    {
        return Uuid::uuid3(Uuid::NAMESPACE_DNS, $name);
    }

    protected function createBackendEvent(string $storeId, Marking $marking, StoreCollection $stores)
    {
        $event = new Event($storeId, $marking, $stores);
        return $event;
    }

    protected function createPersistEvent(string $storeId, Marking $marking, StoreCollection $stores)
    {
        $event = new PersistEvent($storeId, $marking, $stores);
        return $event;
    }

    protected function dispatchBackendEvent($names, Event $event)
    {
        $dispatcher = $this->dispatcher;
        if (!$dispatcher) {
            return;
        }
        if (is_string($names)) {
            $names = [$names];
        }
        if (!is_array($names)) {
            throw new \JBJ\Common\Exception\FixMeException('$names must be string or array');
        }
        foreach ($names as $name) {
            $fqName = sprintf('backend.%s', $name);
            $dispatcher->dispatch($fqName, $event);
        }
    }

    protected function settingMark(string $storeId, Marking $marking, StoreCollection $stores = null)
    {
        $event = $this->createBackendEvent($storeId, $marking, $stores);
        $this->dispatchBackendEvent('mark.setting', $event);
        return $this;
    }

    protected function newStore(string $storeId, Marking $marking, StoreCollection $stores = null)
    {
        $event = $this->createBackendEvent($storeId, $marking, $stores);
        $this->dispatchBackendEvent('mark.newstore', $event);
        return $this;
    }

    protected function markingSet(string $storeId, Marking $marking, StoreCollection $stores = null)
    {
        $event = $this->createPersistEvent($storeId, $marking, $stores);
        $this->dispatchBackendEvent('mark.persist', $event);
        if ($stores !== $event->getStores()) {
            $stores = $event->getStores();
            $this->stores = $stores;
        }
        $event = $this->createBackendEvent($storeId, $marking, $stores);
        $this->dispatchBackendEvent('mark.set', $event);
        return $this;
    }
}
