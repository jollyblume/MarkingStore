<?php

namespace JBJ\Workflow\Workflow\Marking;

use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Common\Collection\ArrayCollectionInterface;
use JBJ\Common\Collection\KeyAwareCollectionTrait;

class StoreCollection implements StoreCollectionInterface, ArrayCollectionInterface {
    use KeyAwareCollectionTrait;

    private $storesId;

    public function __construct(string $storesId, array $elements = []) {
        $this->storesId = $storesId;
        $this->setStrictCollectionMembership(true);
        $this->setKeyAwarePropertyNames('storeId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getStoresId() {
        return $this->storesId;
    }
}
