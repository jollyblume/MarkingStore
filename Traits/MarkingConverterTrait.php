<?php

namespace JBJ\Workflow\Traits;

trait MarkingConverterTrait {
    protected function convertPlacesToKeys(array $places) {
        if (empty($places)) {
            return $places;
        }
        if (isset($places[0])) {
            $places = array_flip($places);
        }
        foreach ($places as $key => $value) {
            $places[$key] = 1;
        }
        return $places;
    }
}
