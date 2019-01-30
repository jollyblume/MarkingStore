<?php

namespace JBJ\Workflow;

interface MarkingInterface extends BaseMarkingInterface
{
    public function getMarkingId();
    public function getMarkingStore();
    public function setMarkingStore(?MarkingStoreInterface $store);
}
