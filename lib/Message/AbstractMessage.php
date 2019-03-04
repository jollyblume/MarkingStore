<?php

namespace JBJ\Workflow\MarkingStore\Message;

abstract class AbstractMessage
{
    private $storeName;
    private $property;
    private $subjectUuid;
    private $places

    public function __construct(string $storeName, string $property, string $subjectUuid, array $places = [])
    {
        $this->storeName = $storeName;
        $this->property = $property;
        $this->$subjectUuid = $subjectUuid;
        $this->$places = $places;
    }

    public function getStoreName()
    {
        return $this->storeName;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
