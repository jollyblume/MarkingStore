<?php

namespace JBJ\Workflow\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JBJ\Workflow\Event\WorkflowEvent as Event;

class AuditListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
