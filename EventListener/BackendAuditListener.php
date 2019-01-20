<?php

namespace JBJ\Workflow\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JBJ\Workflow\Event\BackendEvent as Event;

class BackendAuditListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onSetting(Event $event)
    {
        $markingId = $event->getMarking()->getMarkingId();
        $storeId = $event->getStoreId();
        $this->logger->info(sprintf('Setting marking "%s" to store "%s".', $markingId, $storeId));
    }

    public function onNewStore(Event $event)
    {
        $markingId = $event->getMarking()->getMarkingId();
        $storeId = $event->getStoreId();
        $this->logger->info(sprintf('New marking store created while setting marking "%s" to store "%s".'), $markingId, $storeId);
    }

    public function onSet(Event $event)
    {
        $markingId = $event->getMarking()->getMarkingId();
        $storeId = $event->getStoreId();
        $this->logger->info(sprintf('Marking "%s" set to store "%s".', $markingId, $storeId));
    }

    public static function getSubscribedEvents()
    {
        return [
            'backend.mark.setting' => ['onSetting'],
            'backend.mark.newstore' => ['onNewStore'],
            'backend.mark.set' => ['onSet'],
        ];
    }
}
