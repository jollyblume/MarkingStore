<?php

namespace JBJ\Workflow\MarkingStore\Message;

class StoreCreated extends AbstractMessage
{
    public function __construct(string $storeName, string $property)
    {
        parent::__construct($storeName, $property, '');
    }
}
