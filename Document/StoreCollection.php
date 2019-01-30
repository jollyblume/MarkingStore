<?php

namespace JBJ\Workflow\Workflow\Marking;

use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Collection\GraphCollectionTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class StoreCollection implements StoreCollectionInterface, ArrayCollectionInterface
{
    use GraphCollectionTrait;

    private $storesId;

    private $parent;

    public function __construct(string $name, array $elements = [], PropertyAccessorInterface $propertyAccessor = null)
    {
        $rules = [
            'name' => [
                'markingStoreId',
                'isDisabled' => false,
                'isValid' => true,
            ],
            'parent' => [
                'stores',
                'isDisabled' => false,
                'isValid' => true,
            ],
        ];
        $this->initCollection($storesId, $elements, $rules, $propertyAccessor);
    }

    public function getStoresId()
    {
        return $this->storesId;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(?ArrayCollectionInterface $parent)
    {
        $this->parent = $parent;
    }
}
