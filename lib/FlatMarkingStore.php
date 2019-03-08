<?php

namespace JBJ\Workflow\MarkingStore;

class FlatMarkingStore implements MarkingStoreInterface
{
    private $markings = [];

    public function __construct($markings = null)
    {
        if (null !== $markings) {
            foreach ((array) $markings as $marking) {
                $this->setMarking($marking);
            }
        }
    }

    protected function getMarkingName(string $storeName, string $property, string $subjectUuid): string
    {
        $markingName = rtrim(sprintf('%s/%s/%s', $storeName, $property, $subjectUuid), '/');
        return $markingName;
    }

    public function getMarking(string $storeName, string $property, string $subjectUuid): Marking
    {
        $markings = $this->markings;
        $markingName = $this->getMarkingName($storeName, $property, $subjectUuid);
        $marking = array_key_exists($markingName, $markings) ? $markings[$markingName] : new Marking($storeName, $property, $subjectUuid);
        return $marking;
    }

    public function setMarking(Marking $marking): MarkingStoreInterface
    {
        if (!empty($marking->getPlaces())) {
            $this->markings[strval($marking)] = $marking;
        }
        return $this;
    }
}
