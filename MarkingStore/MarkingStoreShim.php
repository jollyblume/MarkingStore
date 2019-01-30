<?php

namespace JBJ\Workflow\MarkingStore;

use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface as BaseStoreInterface;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use JBJ\Workflow\BackendInterface;
use JBJ\Workflow\MarkingStoreInterface;
use JBJ\Workflow\Traits\MarkingConverterTrait;

class MarkingStoreShim implements BaseStoreInterface, MarkingStoreInterface
{
    use MarkingConverterTrait;

    private $property;
    private $propertyAccessor;
    private $backend;
    private $markingStoreId;

    public function __construct(BackendInterface $backend, string $property = 'subjectId', PropertyAccessorInterface $propertyAccessor = null )
    {
        $this->backend = $backend;
        $this->property = $property;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        $this->markingStoreId = $backend->createId();
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
            throw new \JBJ\Common\Exception\FixMeException("SubjectId not readable");
        }
    }

    public function getMarking($subject)
    {
        $this->assertValidSubject($subject);
        $markingStoreId = $this->getMarkingStoreId();
        $subjectId = $this->getSubjectId($subject);
        $backend = $this->backend;
        $marking = $backend->getMarking($markingStoreId, $subjectId);
        if (!$marking) {
            $marking = new Marking();
        }
        return $marking;
    }

    public function setMarking($subject, Marking $marking)
    {
        $this->assertValidSubject($subject);
        $markingStoreId = $this->getMarkingStoreId();
        $subjectId = $this->getSubjectId($subject);
        $backend = $this->backend;
        $backend->setMarking($markingStoreId, $subjectId, $marking);
    }

    protected function getSubjectId($subject)
    {
        $this->assertValidSubject($subject);
        $property = $this->property;
        $propertyAccessor = $this->propertyAccessor;
        $subjectId = $propertyAccessor->getValue($subject, $property);
        if (!$subjectId) {
            $backend = $this->backend;
            $subjectId = $backend->createId();
            $propertyAccessor->setValue($subject, $property, $subjectId);
        }
        return $subjectId;
    }
}
