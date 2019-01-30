<?php

namespace JBJ\Workflow\Document;

use JBJ\Workflow\MarkingStoreInterface;
use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Collection\GraphCollectionTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class MarkingStore implements MarkingStoreInterface, ArrayCollectionInterface
{
    use GraphCollectionTrait;

    private $markingStoreId;

    private $stores;

    public function __construct(string $name, array $elements = [], PropertyAccessorInterface $propertyAccessor = null)
    {
        $rules = [
            'name' => [
                'markingId',
                'isDisabled' => false,
                'isValid' => true,
            ],
            'parent' => [
                'markingStore',
                'isDisabled' => false,
                'isValid' => true,
            ],
        ];
        $this->initCollection($markingStoreId, $elements, $rules, $propertyAccessor);
    }

    public function getMarkingStoreId()
    {
        $markingStoreId = $this->name;
        return $markingStoreId;
    }

    public function getStores()
    {
        return $this->stores;
    }

    public function setStores(?StoreCollectionInterface $stores)
    {
        $this->stores = $stores;
    }
}
