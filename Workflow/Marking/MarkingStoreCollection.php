<?php

namespace JBJ\Workflow\Workflow\Marking;

use JBJ\Workflow\Collection\ComposedArrayCollectionInterface;
use JBJ\Workflow\Collection\KeyAwareComposedArrayCollectionTrait;

class MarkingStoreCollection implements ComposedArrayCollectionInterface, MarkingStoreCollectionInterface {
    use KeyAwareComposedArrayCollectionTrait;

    private $markingStoreCollectionId;

    public function __construct(string $markingStoreCollectionId, array $elements = []) {
        $this->markingStoreCollectionId = $markingStoreCollectionId;
        $this->setStrictCollectionMembership(true);
        $this->setKeyAwarePropertyNames('markingStoreId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getMarkingStoreCollectionId() {
        return $this->markingStoreCollectionId;
    }
}
