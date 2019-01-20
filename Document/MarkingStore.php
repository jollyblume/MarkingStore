<?php

namespace JBJ\Workflow\Workflow\Marking;

use JBJ\Workflow\MarkingStoresInterface;
use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Collection\KeyAwareCollectionTrait;

class MarkingStore implements MarkingStoresInterface, ArrayCollectionInterface {
    use KeyAwareCollectionTrait;

    private $storeId;

    public function __construct(string $storeId, array $elements = []) {
        $this->storeId = $storeId;
        $this->setStrictCollectionMembership(true);
        $this->setKeyAwarePropertyNames('markingId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getMarkingStoreId() {
        $storeId = $this->storeId;
        return storeId;
    }
}
