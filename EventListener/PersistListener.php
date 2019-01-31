<?php

namespace JBJ\Workflow\EventListener;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\Event\BackendEvent as Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistListener implements EventSubscriberInterface
{
    private $logger;
    private $strategy;

    public function __construct(LoggerInterface $logger, PersistStrategyInterface $strategy = null)
    {
        $this->logger = $logger;
        $this->strategy = $strategy;
    }

    public function onGet(Event $event)
    {
    }

    public function onSetting(Event $event)
    {
    }

    public function onSet(Event $event)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.backend.get' => ['onGet'],
            'workflow.backend.setting' => ['onSetting'],
            'workflow.backend.set' => ['onSet'],
        ];
    }
}
