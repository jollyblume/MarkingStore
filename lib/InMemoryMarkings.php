<?php

namespace JBJ\Workflow\MarkingStore;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\Traits\NameTrait;
use JBJ\Workflow\Traits\CreateIdTrait;

class InMemoryMarkings
{
    use NameTrait, CreateIdTrait;

    private $markings;

    public function __construct(string $name, array $markings = [])
    {
        $this->setName($name ?: $this->createId($name));
        $this->markings = $markings;
    }

    protected function buildIndex(string $storeName, string $subjectUuid, string $property)
    {
        $index = sprintf('%s/%s/%s', $storeName, $subjectUuid, $property);
        return rtrim($index, '/');
    }

    public function getPlaces(string $storeName, string $subjectUuid, string $property)
    {
        $index = $this->buildIndex($storeName, $subjectUuid, $property);
        if (!array_key_exists($index, $this->markings)) {
            return [];
        }
        return $this->markings[$index] ?: [];
    }

    public function setPlaces(string $storeName, string $subjectUuid, string $property, $places)
    {
        $index = $this->buildIndex($storeName, $subjectUuid, $property);
        $places = (array) $places;
        if (empty($places)) {
            unset($this->markings[$index]);
        }
        if (!empty($places)) {
            $this->markings[$index] = $places;
        }
        return $this;
    }

    public function getMarkings()
    {
        return $this->markings;
    }
}
