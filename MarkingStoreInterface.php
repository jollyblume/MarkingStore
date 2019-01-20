<?php

namespace JBJ\Workflow;

 /*
 * forked from symfony/workflow
 *
 * MarkingStoreInterface is the interface between the Workflow Component and a
 * plain old PHP object: the subject.
 *
 * It converts the Marking into something understandable by the subject and vice
 * versa.
 */
interface MarkingStoreInterface
{
    /**
     * @return string $storeId
     */
    public function getStoreId();

    public function getStores();
    public function setStores(StoreCollectionInterface $stores);
}
