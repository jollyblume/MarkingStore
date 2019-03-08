<?php

namespace JBJ\Workflow\MarkingStore;

class FlatMarkings implements MarkingStoreInterface
{
    private $markings = [];

    public function __construct(Marking ...$markings)
    {
        foreach ($markings as $marking) {
            if (!empty($marking->getPlaces())) {
                $this->markings[strval($marking)] = $marking;
            }
        }
    }

    public function getMarkingName(string $storeName, string $property, string $subjectUuid)
    {
        $markingName = rtrim(sprintf('%s/%s/%s', $storeName, $property, $subjectUuid), '/');
        return $markingName;
    }

    public function getMarking(string $storeName, string $property, string $subjectUuid)
    {
        $markings = $this->markings;
        $markingName = $this->getMarkingName($storeName, $property, $subjectUuid);
        $marking = array_key_exists($markingName, $markings) ? $markings[$markingName] : null;
        return $marking;
    }

    public function setMarking(Marking $marking)
    {
        $this->markings[strval($marking)] = $marking;
        return $this;
    }

    public function getPlaces(string $storeName, string $property, string $subjectUuid)
    {
        $marking = $this->getMarking($storeName, $property, $subjectUuid);
        $places = null === $marking ? [] : $marking->getPlaces();
        return $places;
    }

    public function setPlaces(string $storeName, string $property, string $subjectUuid, string ...$places)
    {
        $markingName = $this->getMarkingName($storeName, $property, $subjectUuid);
        unset($this->markings[$markingName]);
        if (!empty($places)) {
            $this->setMarking(new Marking($storeName, $property, $subjectUuid, ...$places));
        }
        return $this;
    }
}
