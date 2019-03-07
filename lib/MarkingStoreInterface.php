<?php

namespace JBJ\Workflow\MarkingStore;

interface MarkingStoreInterface
{
    public function getMarking(string $storeName, string $property, string $subjectUuid);
    public function setMarking(Marking $marking);
    public function getPlaces(string $storeName, string $subjectUuid, string $property);
    public function setPlaces(string $storeName, string $subjectUuid, string $property, string ...$places);
}
