<?php

namespace JBJ\Workflow;

use JBJ\Workflow\

interface MarkingInterface extends BaseMarkingInterface {
    public function getMarkingId();
    public function getStore();
    public function setStore(MarkingStoreInterface $store);
}
