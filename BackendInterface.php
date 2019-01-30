<?php

namespace JBJ\Workflow;

use Symfony\Component\Workflow\Marking;

interface BackendInterface
{
    public function getBackendId();

    public function getMarking(string $markingStoreId, string $subjectId);

    public function setMarking(string $markingStoreId, string $subjectId, Marking $marking);

    public function createId() :string;
}
