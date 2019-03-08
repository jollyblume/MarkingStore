<?php

namespace JBJ\Workflow\MarkingStore;

interface MarkingStoreInterface
{
    public function getMarking(string $storeName, string $property, string $subjectUuid): Marking;
    public function setMarking(Marking $marking): MarkingStoreInterface;
}
