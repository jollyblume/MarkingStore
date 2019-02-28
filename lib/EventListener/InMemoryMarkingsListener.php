<?php

namespace JBJ\Workflow\MarkingStore\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JBJ\Workflow\MarkingStore\Event\MarkingStoreEvent;
use JBJ\Workflow\MarkingStore\MarkingStore\InMemoryMarkings;

class InMemoryMarkingsListener implements EventSubscriberInterface
{
    private $logger;
    private $markings;

    public function __construct(LoggerInterface $logger, InMemoryMarkings $markings = null)
    {
        $this->logger = $logger;
        $this->markings = $markings;
    }

    public function onGet(MarkingStoreEvent $event)
    {
        $storeName = $event->getStoreName();
        $subjectUuid = $event->getSubjectUuid();
        $property = $event->getProperty();
        $places = $this->markings->getPlaces($storeName, $subjectUuid, $property);
        $event->setPlaces($places);
    }

    public function onSetting(MarkingStoreEvent $event)
    {
        $storeName = $event->getStoreName();
        $subjectUuid = $event->getSubjectUuid();
        $property = $event->getProperty();
        $places = $event->getPlaces();
        $this->markings->setPlaces($storeName, $subjectUuid, $property, $places);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.places.get' => ['onGet'],
            'workflow.places.setting' => ['onSetting'],
        ];
    }
}
