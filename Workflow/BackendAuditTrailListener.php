<?php

namespace JBJ\Workflow\Workflow;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JBJ\Workflow\Workflow\BackendEvent as Event;

class BackendAuditTrailListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onSetting(Event $event)
    {
        foreach ($event->getTransition()->getFroms() as $place) {
            $markingId = $event->getMarking()->getMarkingId();
            $storeId = $event->getStoreId();
            $this->logger->info(sprintf('Setting marking "%s" to store "%s".', $markingId, $storeId));
        }
    }

    public function onNewStore(Event $event)
    {
        $this->logger->info(sprintf('New marking store created while setting marking "%s" to store "%s".'), $markingId, $storeId);
    }

    public function onSet(Event $event)
    {
        foreach ($event->getTransition()->getTos() as $place) {
            $this->logger->info(sprintf('Set marking "%s" to store "%s".', $markingId, $storeId));
        }
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
