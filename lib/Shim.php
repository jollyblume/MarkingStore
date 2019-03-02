<?php

namespace JBJ\Workflow\MarkingStore;

use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
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
        $success = $mediator->notifyCreated($name, $property);
        if (!$success) {
            throw new DomainException(sprintf('Event dispatcher not set on mediator "%s"', strval($mediator)));
        }
        $this->mediator = $mediator;
    }

    public function getProperty()
    {
        return $this->property;
    }

    protected function assertSubjectIsReadable($subject)
    {
        $property = $this->getProperty();
        $propertyAccessor = $this->getPropertyAccessor();
        $isReadable = $propertyAccessor->isReadable($subject, $property);
        if (!$isReadable) {
            throw new InvalidArgumentException("The subject's uuid is not readable.");
        }
    }

    protected function assertSubjectIsWritable($subject)
    {
        $property = $this->getProperty();
        $propertyAccessor = $this->getPropertyAccessor();
        $isWritable = $propertyAccessor->isWritable($subject, $property);
        if (!$isWritable) {
            throw new InvalidArgumentException("The subject's uuid is not writable.");
        }
    }

    protected function assertSubjectIsObject($subject)
    {
        if (!is_object($subject)) {
            throw new DomainException('The subject must be an object');
        }
    }

    protected function getSubjectUuid($subject)
    {
        $this->assertSubjectIsObject($subject);
        $this->assertSubjectIsReadable($subject);
        $propertyAccessor = $this->getPropertyAccessor();
        $property = $this->getProperty();
        $subjectUuid = $propertyAccessor->getValue($subject, $property);
        if (!$subjectUuid) {
            $this->assertSubjectIsWritable($subject);
            $subjectUuid = $this->mediator->createUuid();
            $propertyAccessor->setValue($subject, $property, $subjectUuid);
        }
        return $subjectUuid;
    }

    public function getMarking($subject)
    {
        $property = $this->getProperty();
        $subjectUuid = $this->getSubjectUuid($subject);
        $storeName = $this->getName();
        $mediator = $this->mediator;
        $places = $mediator->getPlaces($storeName, $subjectUuid, $property);
        $transformer = new MarkingToPlacesTransformer();
        $marking = $transformer->reverseTransform($places);
        return $marking;
    }

    public function setMarking($subject, Marking $marking)
    {
        $property = $this->getProperty();
        $subjectUuid = $this->getSubjectUuid($subject);
        $storeName = $this->getName();
        $transformer = new MarkingToPlacesTransformer();
        $places = $transformer->transform($marking);
        $mediator = $this->mediator;
        $mediator->setPlaces($storeName, $subjectUuid, $property, $places);
        return $this;
    }
}
