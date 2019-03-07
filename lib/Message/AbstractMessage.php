<?php

namespace JBJ\Workflow\MarkingStore\Message;

abstract class AbstractMessage
{
    private $storeName;
    private $property;
    private $subjectUuid;

    public function __construct(string $storeName, string $property, string $subjectUuid)
    {
        $this->storeName = $storeName;
        $this->property = $property;
        $this->subjectUuid = $subjectUuid;
    }

    public function getName()
    {
        $storeName = $this->storeName;
        $property = $this->property;
        $subjectUuid = $this->subjectUuid;
        $name = rtrim(sprintf('%s/%s/%s', $storeName, $property, $subjectUuid), '/');
        return $name;
    }

    public function getStoreName()
    {
        return $this->storeName;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function getSubjectUuid()
    {
        return $this->subjectUuid;
    }

    public function toString()
    {
        return $this->getName();
    }
}
