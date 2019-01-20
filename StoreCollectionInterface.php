<?php

namespace JBJ\Workflow;

use JBJ\Common\Collection\ArrayCollectionInterface;

/**
 * StoreCollectionInterface
 *
 * Implementations are a collection of marking stores
 */
interface StoreCollectionInterface
{
    /**
     * @return string $storeId
     */
    public function getStoresId();

    public function getStoresParent();
    public function setStoresParent(ArrayCollectionInterface $stores);
}
