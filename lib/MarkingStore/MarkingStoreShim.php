<?php

namespace JBJ\Workflow\MarkingStore\MarkingStore;

use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface as BaseStoreInterface;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use JBJ\Workflow\MarkingStore\Event\MarkingStoreEvent;
use JBJ\Workflow\MarkingStore\MarkingStoreInterface;
use JBJ\Workflow\MarkingStore\Transformer\MarkingToPlacesTransformer;
use JBJ\ComposedCollections\Traits\ElementNameTrait;
use JBJ\ComposedCollections\Traits\CreateIdTrait;

class MarkingStoreShim implements BaseStoreInterface, MarkingStoreInterface
{
    use ElementNameTrait, CreateIdTrait;

    private $property;
    private $propertyAccessor;
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, PropertyAccessorInterface $propertyAccessor = null, string $property = 'subjectId', string $name = '')
    {
        $this->dispatcher = $dispatcher;
        $this->property = $property;
        if ('marking' === $property) {
            throw new \JBJ\Workflow\Exception\FixMeException('property named "marking" is reserved for symfony/workflow');
        }
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        //todo send event to audit this name change
        //todo does this name change belong here?
        $this->setName($this->createId($name));
        $event = new MarkingStoreEvent($this->getName());
        $dispatcher->dispatch('workflow.store.created', $event);
    }

    public function getMarkingStoreId()
    {
        return $this->getName();
    }

    protected function assertValidSubject($subject)
    {
        $property = $this->property;
        $propertyAccessor = $this->propertyAccessor;
        $isReadable = $propertyAccessor->isReadable($subject, $property);
        $isWritable = $propertyAccessor->isWritable($subject, $property);
        if (!$isReadable || !$isWritable) {
            throw new \JBJ\Workflow\Exception\FixMeException("SubjectId not readable or writable.");
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
        $transformer = new MarkingToPlacesTransformer();
        $marking = $transformer->reverseTransform($places);
        return $marking;
    }

    public function setMarking($subject, Marking $marking)
    {
        $this->assertValidSubject($subject);
        $markingStoreId = $this->getMarkingStoreId();
        $subjectId = $this->getSubjectId($subject);
        $transformer = new MarkingToPlacesTransformer();
        $places = $transformer->transform($marking);
        $this->setPlaces($markingStoreId, $subjectId, $places);
    }

    protected function getPlaces(string $markingStoreId, string $subjectId)
    {
        $event = new MarkingStoreEvent($markingStoreId, $subjectId);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch('workflow.places.get', $event);
        $places = $event->getPlaces();
        return $places;
    }

    protected function setPlaces(string $markingStoreId, string $subjectId, array $places)
    {
        $event = new MarkingStoreEvent($markingStoreId, $subjectId, $places);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch('workflow.places.setting', $event);
        $dispatcher->dispatch('workflow.places.set', $event);
    }
}
