<?php

namespace JBJ\Workflow\EventListener;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\Event\WorkflowEvent;
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

    public function onStoreCreated(WorkflowEvent $event)
    {
        $markingStoreId = $event->getMarkingStoreId();
        $subjectId = $event->getSubjectId();
        $places = $event->getPlaces();
        $this->strategy->storeCreated($markingStoreId, $subjectId, $places);
    }

    public function onGet(WorkflowEvent $event)
    {
        $markingStoreId = $event->getMarkingStoreId();
        $subjectId = $event->getSubjectId();
        $this->strategy->getPlaces($markingStoreId, $subjectId);
    }

    public function onSetting(WorkflowEvent $event)
    {
        $markingStoreId = $event->getMarkingStoreId();
        $subjectId = $event->getSubjectId();
        $places = $event->getPlaces();
        $this->strategy->setPlaces($markingStoreId, $subjectId, $places);
    }

    public function onSet(WorkflowEvent $event)
    {
        $markingStoreId = $event->getMarkingStoreId();
        $subjectId = $event->getSubjectId();
        $places = $event->getPlaces();
        $this->strategy->cleanup($markingStoreId, $subjectId, $places);
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
