<?php

namespace JBJ\Workflow\MarkingStore\MarkingStore;

interface MediatorInterface
{
    public function getPlaces(string $storeName, string $subjectUuid, string $property);
    public function setPlaces(string $storeName, string $subjectUuid, string $property, $places);
    public function getPropertyAccessor();
    public function getDefaultProperty();
    public function createUuid($name);
    public function dispatchCreated(string $name, string $property);
}
