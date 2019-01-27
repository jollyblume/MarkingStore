<?php

namespace JBJ\Workflow\Workflow\Marking;

use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Collection\GraphCollectionTrait;

class StoreCollection implements StoreCollectionInterface, ArrayCollectionInterface {
    use GraphCollectionTrait;

    private $storesId;

    private $parent;

    public function __construct(string $storesId, array $elements = []) {
        $this->storesId = $storesId;
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
        $this->initCollection($elements, $rules, true, true);
    }

    public function getStoresId() {
        return $this->storesId;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent(?ArrayCollectionInterface $parent) {
        $this->parent = $parent;
    }
}
