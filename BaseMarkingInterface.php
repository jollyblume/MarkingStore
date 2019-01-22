<?php

namespace JBJ\Workflow;

interface BaseMarkingInterface {
    public function mark($place);
    public function unmark($place);
    public function has($place);
    public function getPlaces();
}
