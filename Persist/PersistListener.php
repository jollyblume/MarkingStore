<?php

namespace JBJ\Workflow\Workflow\Persist;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\Workflow\Marking\MarkingStoreCollectionInterface as StoreCollectionInterface;
use JBJ\Workflow\Workflow\BackEndPersistEvent as Event;
use JBJ\Workflow\Traits\PropertyAccessorTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistListener implements EventSubscriberInterface
{
    use PropertyAccessorTrait;

    const STATUS_PROPERTY_NAME = 'persistStatus';
    const STATUS_DISABLED = '__DISABLED__';
    const STATUS_PERSISTABLE = '__PERSISTABLE__';
    const STATUS_MANAGED = '__MANAGED__';
    const STATUS_UNKNOWN = '__UNKNOWN__';

    private $logger;
    private $strategy;

    public function __construct(LoggerInterface $logger, PersistStrategyInterface $strategy = null)
    {
        $this->logger = $logger;
        $this->strategy = $strategy;
    }

    protected function isPersistStatusReadable(Event $event)
    {
        $stores = $event->getStores();
        $isReadable = $this->isPropertyValueReadable($stores, self::STATUS_PROPERTY_NAME);
        return $isReadable;
    }

    protected function getPersistStatus(Event $event)
    {
        $isReadable = $this->isPersistStatusReadable($event);
        if (!$isReadable) {
            return self::STATUS_DISABLED;
        }
        $stores = $event->getStores();
        $status = $this->getPropertyValue($stores, self::STATUS_PROPERTY_NAME);
        foreach ([self::STATUS_DISABLED, self::STATUS_PERSISTABLE, self::STATUS_MANAGED] as $allowed) {
            if ($allowed === $status) {
                return $status;
            }
        }
        return self::STATUS_UNKNOWN;
    }

    protected function setPersistStatus(Event $event, string $status)
    {
        $isReadable = $this->isPersistStatusReadable($stores);
        if (!$isReadable) {
            throw new \JBJ\Common\Exception\FixMeException('not readable');
        }
        foreach ([self::STATUS_DISABLED, self::STATUS_PERSISTABLE, self::STATUS_MANAGED] as $allowed) {
            if ($allowed === $status) {
                $stores = $event->getStores();
                $this->setPropertyValue($stores, self::STATUS_PROPERTY_NAME, $status);
                return $this;
            }
        }
        throw new \JBJ\Common\Exception\FixMeException('invalid status');
    }

    protected function isMigrationDisabled(Event $event)
    {
        $stores = $event->getStores();
        return $this->strategy->isMigrationDisabled($stores);
    }

    protected function isMigrationValid(Event $event)
    {
        $stores = $event->getStores();
        return $this->strategy->isMigrationValid($stores);
    }

    protected function isMigrated(Event $event)
    {
        $stores = $event->getStores();
        return $this->strategy->isMigrated($stores);
    }

    protected function hasMigrationPath(Event $event)
    {
        $stores = $event->getStores();
        return $this->strategy->hasMigrationPath($stores);
    }

    protected function executeMigration(Event $event)
    {
        $stores = $event->getStores();
        return $this->strategy->executeMigration($stores);
    }

    protected function persist(Event $event)
    {
        $stores = $event->getStores();
        $markingsId = $event->getMarkingStoreId();
        $marking = $event->getMarking();
        $this->strategy->persist($stores, $markingsId, $marking);
    }

    protected function flush(Event $event)
    {
        $stores = $event->getStores();
        return $this->strategy->flush($stores);
    }

    protected function handleStatusUnknown(Event $event)
    {
        $this->setPersistStatus($event, self::STATUS_PERSISTABLE);
        $hasPath = $this->hasMigrationPath($event);
        if (!$hasPath) {
            throw new \JBJ\Common\Exception\FixMeException('no migration path found');
        }
        $stores = $this->executeMigration($event);
        if ($stores !== $event->getStores()) {
            $event->setStores($stores);
            $storesId = $event->getMarkingStoreCollectionId();
            $this->logger->info(sprintf('Marking store "%s" migrated', $storesId));
        }
        $status = self::STATUS_MANAGED;
        $this->setPersistStatus($status);
        return $status;
    }

    public function onPersist(Event $event)
    {
        $strategy = $this->strategy;
        if (!$strategy) {
            // log persist disabled no strategy
            return;
        }
        $status = $this->getPersistStatus($event);
        if ($status === self::STATUS_DISABLED) {
            $storesId = $event->getMarkingStoreCollectionId();
            $this->logger->warn(sprintf('Marking store "%s" disabled', $storesId));
            return;
        }
        if ($status === self::STATUS_PERSISTABLE) {
            throw new \JBJ\Common\Exception\FixMeException('logic exception: should never start onPersist in this status');
        }
        $isMigrated = $this->isMigrated($event);
        if ($status !== self::STATUS_MANAGED && $isMigrated) {
            //todo log unmanged, but migrated stores set to managed
            $this->setPersistStatus($event, self::STATUS_MANAGED);
            $status = self::STATUS_MANAGED;
        }
        if ($status !== self::STATUS_MANAGED) {
            $status = $this->handleStatusUnknown($event);
        }
        if ($status !== self::STATUS_MANAGED) {
            $this->setPersistStatus($event, self::STATUS_DISABLED);
            $storesId = $event->getMarkingStoreCollectionId();
            $this->logger->warn(sprintf('Marking store "%s" disabled: unknown reason', $storesId));
            return;
        }
        $stores = $event->getStores();
        $markingStoreId = $event->getMarkingStoreId();
        $marking = $event->getMarking();
        $strategy->persist($stores, $markingStoreId, $marking);
    }

    public static function getSubscribedEvents()
    {
        return [
            'backend.mark.persist' => ['onPersist'],
        ];
    }
}
