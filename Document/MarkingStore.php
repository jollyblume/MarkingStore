<?php

namespace JBJ\Workflow\Document;

use JBJ\Workflow\MarkingStoreInterface;
use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Collection\GraphCollectionTrait;

class MarkingStore implements MarkingStoreInterface, ArrayCollectionInterface {
    use GraphCollectionTrait;

    private $markingStoreId;

    private $stores;

    public function __construct(string $markingStoreId, array $elements = []) {
        $this->markingStoreId = $markingStoreId;
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
        $this->initCollection($elements, $rules, true, true);
    }

    public function getMarkingStoreId() {
        $markingStoreId = $this->markingStoreId;
        return $markingStoreId;
    }

    public function getStores() {
        return $this->stores;
    }

    public function setStores(?StoreCollectionInterface $stores) {
        $this->stores = $stores;

    }
}
