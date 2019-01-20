<?php

namespace JBJ\Workflow\Document;

use JBJ\Workflow\BaseMarkingInterface;
use JBJ\Workflow\MarkingInterface;
use JBJ\Workflow\Traits\MarkingTrait;

class Marking implements BaseMarkingInterface, MarkingInterface {
    use MarkingTrait;

    /**
     * Marking UUID
     *
     * @var string $markingId
     */
    private $markingId;

    /**
     * List of places
     *
     * @var array $places
     */
    private $places;

    public function __construct(string $markingId, array $places = []) {
        $this->markingId = $markingId;
        $this->markPlaces($places);
    }

    protected function markPlaces(array $places) {
        $places = $this->convertPlacesToKeys($places)
        foreach ($places as $place => $nbToken) {
            $this->mark($place);
        }
    }

    public function getMarkingId() {
        return $this->markingId;
    }

    public function mark($place)
    {
        $this->places[$place] = 1;
    }

    public function unmark($place)
    {
        unset($this->places[$place]);
    }

    public function has($place)
    {
        return isset($this->places[$place]);
    }

    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return self::class . '@' . spl_object_hash($this);
    }
}
