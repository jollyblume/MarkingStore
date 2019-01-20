<?php

namespace JBJ\Workflow\Workflow\Marking;

use Symfony\Component\Workflow\Marking;

interface MultiTenantMarkingStoreBackendInterface {
    /**
     * Get the backendId
     *
     * @return string backendId
     */
    public function getBackendId();
    
    /**
     * Get a workflow marking from the backend
     *
     * @param string $markingStoreId
     * @param string $markingId
     * @return Marking The workflow marking
     */
    public function getMarking(string $markingStoreId, string $markingId);

    /**
     * Persist a workflow marking to the backend
     *
     * @param string $markingStoreId
     * @param Marking $marking The workflow marking
     * @return self
     */
    public function setMarking(string $markingStoreId, Marking $marking);

    /**
     * Create a new Marking id
     *
     * The markingId can be any string that is unique within a workflow domain,
     * but is normally a UUID.
     *
     * Some important workflow domains:
     *  - subject id
     *  - workflow/marking-store id
     *  - workflow/marking-store collection id
     *  - workflow definition
     *
     * @param string $name Name used in UUID5 generation
     * @return string UUID string
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function createId(string $name = 'workflow.general') :string;
}
