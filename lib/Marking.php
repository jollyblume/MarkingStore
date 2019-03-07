<?php

namespace JBJ\Workflow\MarkingStore;

final class Marking
{
    private $storeName;
    private $property;
    private $subjectUuid;
    private $places;

    public function __construct(string $storeName, string $property, string $subjectUuid = '', string ...$places)
    {
        $this->storeName = $storeName;
        $this->property = $property;
        $this->subjectUuid = $subjectUuid;
        $this->places = array_unique($places);
    }

    public function createFrom(string ...$places): Marking
    {
        $storeName = $this->getStoreName();
        $property = $this->getProperty();
        $subjectUuid = $this->getSubjectUuid();
        return new static($storeName, $property, $subjectUuid, ...$places);
    }

    public function getName(): string
    {
        $storeName = $this->getStoreName();
        $property = $this->getProperty();
        $subjectUuid = $this->getSubjectUuid();
        $markingName = rtrim(sprintf('%s/%s/%s', $storeName, $property, $subjectUuid), '/');
        return $markingName;
    }

    public function getStoreName(): string
    {
        return $this->storeName;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getSubjectUuid(): string
    {
        return $this->subjectUuid;
    }

    public function getPlaces(): array
    {
        return $this->places;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
