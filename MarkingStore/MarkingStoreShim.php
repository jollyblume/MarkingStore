<?php

namespace JBJ\Workflow\MarkingStore;

use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface as BaseStoreInterface;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\Uuid;
use JBJ\Workflow\Event\WorkflowEvent;
use JBJ\Workflow\Event\StoreEvent;
use JBJ\Workflow\MarkingStoreInterface;

class MarkingStoreShim implements BaseStoreInterface, MarkingStoreInterface
{
    private $markingStoreId;
    private $property;
    private $propertyAccessor;
    private $dispatcher;

    public function __construct(string $property = 'subjectId', EventDispatcherInterface $dispatcher, PropertyAccessorInterface $propertyAccessor = null )
    {
        $this->property = $property;
        $this->dispatcher = $dispatcher;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        $this->markingStoreId = $this->createId();
        $event = new StoreEvent($this, $subjectId);
        $dispatcher->dispatch('workflow.store.created', $event);
    }

    public function getMarkingStoreId()
    {
        return $this->markingStoreId;
    }

    protected function assertValidSubject($subject)
    {
        $property = $this->property;
        $propertyAccessor = $this->propertyAccessor;
        $isReadable = $propertyAccessor->isReadable($subject, $property);
        $isWritable = $propertyAccessor->isWritable($subject, $property);
        if (!$isReadable || !$isWritable) {
            throw new \JBJ\Common\Exception\FixMeException("SubjectId not readable or writable.");
        }
    }

    protected function getSubjectId($subject)
    {
        $this->assertValidSubject($subject);
        $property = $this->property;
        $propertyAccessor = $this->propertyAccessor;
        $subjectId = $propertyAccessor->getValue($subject, $property);
        if (!$subjectId) {
            $subjectId = $this->createId();
            $propertyAccessor->setValue($subject, $property, $subjectId);
        }
        return $subjectId;
    }

    public function getMarking($subject)
    {
        $this->assertValidSubject($subject);
        $markingStoreId = $this->getMarkingStoreId();
        $subjectId = $this->getSubjectId($subject);
        $places = $this->getPlaces($markingStoreId, $subjectId);
        $marking = new Marking($places);
        return $marking;
    }

    public function setMarking($subject, Marking $marking)
    {
        $this->assertValidSubject($subject);
        $markingStoreId = $this->getMarkingStoreId();
        $subjectId = $this->getSubjectId($subject);
        $this->setPlaces($markingStoreId, $subjectId, $marking->getPlaces());
    }

    protected function getPlaces(string $markingStoreId, string $subjectId)
    {
        $event = new WorkflowEvent($markingStoreId, $subjectId);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch('workflow.places.get', $event);
        $places = $event->getPlaces();
        return $places;
    }

    protected function setPlaces(string $markingStoreId, string $subjectId, array $places)
    {
        $event = new WorkflowEvent($markingStoreId, $subjectId, $places);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch('workflow.places.setting', $event);
        $dispatcher->dispatch('workflow.places.set', $event);
    }

    protected function createId()
    {
        return Uuid::uuid4();
    }
}
