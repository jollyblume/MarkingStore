<?php

namespace JBJ\Workflow\MarkingStore;

use Symfony\Component\Workflow\MarkingStoreInterface;
use Symfony\Component\Workflow\Marking;
use JBJ\Workflow\MarkingStore\Transformer\MarkingToPlacesTransformer;
use JBJ\Workflow\Traits\NameTrait;
use JBJ\Workflow\Traits\PropertyAccessorTrait;
use JBJ\Workflow\Exception\InvalidArgumentException;
use JBJ\Workflow\Exception\DomainException;

class Shim implements MarkingStoreInterface
{
    use NameTrait, PropertyAccessorTrait {
        setPropertyAccessor as protected;
    }

    private $mediator;
    private $property;

    public function __construct(MediatorInterface $mediator, string $name = '', string $property = '')
    {
        $this->setPropertyAccessor($mediator->getPropertyAccessor());
        $this->setName($name ?: $mediator->createUuid());
        $this->property = $property ?: $mediator->getDefaultProperty();
        if ('marking' === $property) {
            throw new InvalidArgumentException('The property named "marking" is reserved for symfony/workflow');
        }
        $success = $mediator->notifyCreated($name, $property);
        if (!$success) {
            throw new DomainException('Event dispatcher not set on mediator "%s"', strval($mediator));
        }
        $this->mediator = $mediator;
    }

    public function getProperty()
    {
        return $this->property;
    }

    protected function assertValidSubject($subject)
    {
        $property = $this->getProperty();
        $propertyAccessor = $this->getPropertyAccessor();
        $isReadable = $propertyAccessor->isReadable($subject, $property);
        $isWritable = $propertyAccessor->isWritable($subject, $property);
        if (!$isReadable || !$isWritable) {
            throw new DomainException("The subject's uuid is either not readable or not writable.");
        }
    }

    protected function getSubjectUuid($subject)
    {
        $this->assertValidSubject($subject);
        $property = $this->getProperty();
        $propertyAccessor = $this->getPropertyAccessor();
        $subjectUuid = $propertyAccessor->getValue($subject, $property);
        if (!$subjectUuid) {
            $subjectUuid = $this->mediator->createUuid();
            $propertyAccessor->setValue($subject, $property, $subjectUuid);
        }
        return $subjectUuid;
    }

    public function getMarking($subject)
    {
        $subjectUuid = $this->getSubjectUuid($subject);
        $storeName = $this->getName();
        $property = $this->getProperty();
        $mediator = $this->mediator;
        $places = $mediator->getPlaces($storeName, $subjectUuid, $property);
        if (false === $places) {
            throw new DomainException('Event dispatcher not set on mediator "%s"', strval($mediator));
        }
        $transformer = new MarkingToPlacesTransformer();
        $marking = $transformer->reverseTransform($places);
        return $marking;
    }

    public function setMarking($subject, Marking $marking)
    {
        $subjectUuid = $this->getSubjectId($subject);
        $storeName = $this->getMarkingStoreId();
        $property = $this->getProperty();
        $transformer = new MarkingToPlacesTransformer();
        $places = $transformer->transform($marking);
        $mediator = $this->mediator;
        $success = $mediator->setPlaces($storeName, $subjectUuid, $property, $places);
        if (!$success) {
            throw new DomainException('Event dispatcher not set on mediator "%s"', strval($mediator));
        }
    }
}
