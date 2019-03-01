<?php

namespace JBJ\Workflow\MarkingStore;

use JBJ\Workflow\MarkingStoreInterface as BaseInterface;

interface MarkingStoreInterface implements BaseInterface
{
    /**
     * @return string $storeId
     */
    public function getName();
}
