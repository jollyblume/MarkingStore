<?php

namespace JBJ\Workflow\MarkingStore\MarkingStore;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\Workflow\Marking;
use JBJ\Workflow\MarkingStore\Transformer\MarkingToPlacesTransformer;
use JBJ\Workflow\Traits\CreateIdTrait;
use JBJ\Workflow\Traits\NameTrait;
use JBJ\Workflow\Traits\PropertyAccessorTrait;
use JBJ\Workflow\Exception\InvalidArgumentException;

class Shim implements MarkingStoreInterface
{
    use NameTrait, PropertyAccessorTrait;

    private $mediator;
    private $property;

    public function __construct(MediatorInterface $mediator, string $name = '', string $property = '')
    {
        $this->setPropertyAccessor($mediator->getPropertyAccessor());
        $this->property = $property ?: $mediator->getDefaultProperty();
        if ('marking' === $property) {
            throw new InvalidArgumentException('The property named "marking" is reserved for symfony/workflow');
        }
        $this->setName($name ?: $mediator->createUuid());
        $mediator->notifyCreated($name, $property);
        $this->mediator = $mediator;
    }

    protected function assertValidSubject($subject)
    {
        $property = $this->property;
        $propertyAccessor = $this->getPropertyAccessor();
        $isReadable = $propertyAccessor->isReadable($subject, $property);
        $isWritable = $propertyAccessor->isWritable($subject, $property);
        if (!$isReadable || !$isWritable) {
            throw new \JBJ\Workflow\Exception\FixMeException("SubjectId not readable or writable.");
        }
    }

    protected function getSubjectUuid($subject)
    {
        $this->assertValidSubject($subject);
        $property = $this->property;
        $propertyAccessor = $this->getPropertyAccessor();
        $subjectUuid = $propertyAccessor->getValue($subject, $property);
        if (!$subjectUuid) {
            $subjectUuid = $this->mediator->createId();
            $propertyAccessor->setValue($subject, $property, $subjectUuid);
        }
        return $subjectUuid;
    }

    public function getMarking($subject)
    {
        $subjectUuid = $this->getSubjectUuid($subject);
        $name = $this->getName();
        $property = $this->property;
        $places = $this->mediator->getPlaces($name, $subjectUuid, $property);
        $transformer = new MarkingToPlacesTransformer();
        $marking = $transformer->reverseTransform($places);
        return $marking;
    }

    public function setMarking($subject, Marking $marking)
    {
        $subjectUuid = $this->getSubjectId($subject);
        $name = $this->getMarkingStoreId();
        $property = $this->property;
        $transformer = new MarkingToPlacesTransformer();
        $places = $transformer->transform($marking);
        $this->setPlaces($name, $subjectUuid, $property, $places);
    }
}
