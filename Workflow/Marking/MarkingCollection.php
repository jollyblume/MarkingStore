<?php

namespace JBJ\Workflow\Workflow\Marking;

use JBJ\Workflow\Collection\ComposedArrayCollectionInterface;
use JBJ\Workflow\Collection\KeyAwareComposedArrayCollectionTrait;

class MarkingCollection implements ComposedArrayCollectionInterface {
    use KeyAwareComposedArrayCollectionTrait;

    private $markingStoreId;

    public function __construct(string $markingStoreId, array $elements = []) {
        $this->markingStoreId = $markingStoreId;
        $this->setStrictCollectionMembership(true);
        $this->setKeyAwarePropertyNames('markingId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getMarkingStoreId() {
        $markingStoreId = $this->markingStoreId;
        return $markingStoreId;
    }
}
