<?php

namespace JBJ\Workflow\MarkingStore\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JBJ\Workflow\MarkingStore\Event\MarkingStoreEvent;
use JBJ\Workflow\MarkingStore\InMemoryMarkings;

class InMemoryMarkingsListener implements EventSubscriberInterface
{
    private $logger;
    private $markings;

    protected function logEvent(string $msg, string $method, MarkingStoreEvent $event)
    {
        $context = [
            'storeName' => $event->getStoreName(),
            'subjectUuid' => $event->getSubjectUuid(),
            'property' => $event->getProperty(),
            'mediator' => $event->getMediator(),
            'places' => $event->getPlaces(),
        ];
        $msg = sprintf('[%s] %s', $method, $msg);
        $this->logger->info($msg, $context);
    }

    public function __construct(LoggerInterface $logger, InMemoryMarkings $markings = null)
    {
        $this->logger = $logger;
        $this->markings = $markings;
        $logger->info('[STARTED] InMemoryMarkingsListener');
    }

    public function onCreate(MarkingStoreEvent $event)
    {
        $this->logEvent('exiting', 'onCreate', $event);
    }

    public function onGet(MarkingStoreEvent $event)
    {
        $storeName = $event->getStoreName();
        $subjectUuid = $event->getSubjectUuid();
        $property = $event->getProperty();
        $places = $this->markings->getPlaces($storeName, $subjectUuid, $property);
        $event->setPlaces($places);
        $this->logEvent('exiting', 'onGet', $event);
    }

    public function onSetting(MarkingStoreEvent $event)
    {
        $storeName = $event->getStoreName();
        $subjectUuid = $event->getSubjectUuid();
        $property = $event->getProperty();
        $places = $event->getPlaces();
        $this->markings->setPlaces($storeName, $subjectUuid, $property, $places);
        $this->logEvent('exiting', 'onSetting', $event);
    }

    public function onSet(MarkingStoreEvent $event)
    {
        $this->logEvent('exiting', 'onSet', $event);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.store.created' => ['onCreate'],
            'workflow.places.get' => ['onGet'],
            'workflow.places.setting' => ['onSetting'],
            'workflow.places.set' => ['onSet'],
        ];
    }
}
