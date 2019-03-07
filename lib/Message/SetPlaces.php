<?php

namespace JBJ\Workflow\MarkingStore\Message;

class SetPlaces extends AbstractMessage
{
    private $places;

    public function __construct(string $storeName, string $property, string $subjectUuid, string ...$places)
    {
        parent::__construct($storeName, $property, $subjectUuid);
        $this->places = $places;
    }

    public function getPlaces()
    {
        $this->places;
    }
}
