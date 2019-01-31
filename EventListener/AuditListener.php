<?php

namespace JBJ\Workflow\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JBJ\Workflow\Event\WorkflowEvent;

class AuditListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onStoreCreated(WorkflowEvent $event)
    {
        $markingStoreId = $event->getMarkingStoreId();
        $subjectId = $event->getSubjectId();
        $places = $event->getPlaces();
        $this->logger->info(sprintf('Marking store "%s" created', $markingStoreId));
    }

    public function onGet(WorkflowEvent $event)
    {
        $markingStoreId = $event->getMarkingStoreId();
        $subjectId = $event->getSubjectId();
        $this->logger->info(sprintf('%s/%s get request', $markingStoreId, $subjectId));
    }

    public function onSetting(WorkflowEvent $event)
    {
        $markingStoreId = $event->getMarkingStoreId();
        $subjectId = $event->getSubjectId();
        $places = $event->getPlaces();
        $this->logger->info(sprintf('%s/%s set request', $markingStoreId, $subjectId));
    }

    public function onSet(WorkflowEvent $event)
    {
        $markingStoreId = $event->getMarkingStoreId();
        $subjectId = $event->getSubjectId();
        $places = $event->getPlaces();
        $this->logger->info(sprintf('%s/%s cleanup request', $markingStoreId, $subjectId));
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.store.created' => ['onStoreCreated'],
            'workflow.places.get' => ['onGet'],
            'workflow.places.setting' => ['onSetting'],
            'workflow.places.set' => ['onSet'],
        ];
    }
}
