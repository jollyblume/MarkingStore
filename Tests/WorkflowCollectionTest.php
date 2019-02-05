<?php

namespace JBJ\Workflow\Tests;

use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Tests\Collection\BaseCollectionTraitTest as BaseTestCase;
use JBJ\Workflow\WorkflowCollection;

class BaseCollectionTraitTest extends BaseTestCase
{
    protected function getTestClass() : string
    {
        return WorkflowCollection::class;
    }

    protected function getRules() : array
    {
        $rules = [
            'name' => [
                'name',
                'isDisabled' => false,
                'isValid' => true,
            ],
            'parent' => [
                'parent',
                'isDisabled' => false,
                'isValid' => true,
            ],
        ];
        return $rules;
    }

    protected function createCollection(string $name, array $elements = []) : ArrayCollectionInterface
    {
        $rules = $this->getRules();
        $propertyAccessor = $this->getPropertyAccessor();
        $testClass = $this->getTestClass();
        $collection = new $testClass($elements, $propertyAccessor, $name);
        return $collection;
    }
}
