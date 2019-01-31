<?php

namespace JBJ\Workflow\Tests\Document;

use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Tests\Collection\BaseCollectionTraitTest;
use JBJ\Workflow\Document\InMemoryCollection;

class InMemeryCollectionCompatibilityTest extends BaseCollectionTraitTest
{
    protected function getTestClass() : string
    {
        return InMemoryCollection::class;
    }

    protected function getRules() : array
    {
        return [];
    }

    protected function createCollection(string $name, array $elements = []) : ArrayCollectionInterface
    {
        $testClass = $this->getTestClass();
        $collection = new $testClass($elements);
        return $collection;
    }
}
