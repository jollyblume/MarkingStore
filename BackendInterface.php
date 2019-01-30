<?php

namespace JBJ\Workflow;

interface BackendInterface
{
    public function getBackendId();

    public function getMarking(string $markingStoreId, string $subjectId);

    public function setMarking(string $markingStoreId, string $subjectId, array $places);

    public function createId() :string;
}
