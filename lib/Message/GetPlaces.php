<?php

namespace JBJ\Workflow\MarkingStore\Message;

class GetPlaces extends AbstractMessage
{
    public function __construct(string $storeName, string $property, string $subjectUuid)
    {
        parent::__construct($storeName, $property, $subjectUuid);
    }
}
