<?php

namespace JBJ\Workflow\Document;

use JBJ\Common\Collection\ArrayCollectionInterface;

class InMemoryCollection implements ArrayCollectionInterface
{
    use JBJ\Common\Collection\CollectionTrait;

    public function __construct(array $elements = [])
    {
        if (!empty($elements)) {
            $this->setChildren($elements);
        }
    }
}
