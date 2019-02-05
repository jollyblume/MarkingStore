<?php

namespace JBJ\Workflow;

use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Collection\GraphCollectionTrait;
use JBJ\Workflow\Traits\CreateIdTrait;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Ramsey\Uuid\Uuid;

class WorkflowCollection implements ArrayCollectionInterface
{
    use CreateIdTrait, GraphCollectionTrait;

    public function __construct(array $elements = [], PropertyAccessorInterface $propertyAccessor = null, string $name = '')
    {
        $name = $this->createId($name);
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
        $this->initializeTrait($name, $elements, $rules, $propertyAccessor);
    }
}
