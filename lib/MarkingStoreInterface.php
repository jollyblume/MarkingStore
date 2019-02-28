<?php

namespace JBJ\Workflow\MarkingStore;

use JBJ\Workflow\MarkingStore\MarkingStoreInterface as BaseInterface;

interface MarkingStoreInterface implements BaseInterface
{
    /**
     * @return string $storeId
     */
    public function getName();
}
