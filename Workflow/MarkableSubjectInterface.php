<?php

namespace JBJ\Workflow\Workflow;

/**
 * MarkableSubjectInterface
 *
 * Any class intending to participate in a workflow must implement the methods
 * in MarkableSubjectInterface. It is not necessary to actually implement the
 * interface.
 *
 * Note, that symfony/property-access is used to get/set markingId on a subject.
 * Any class that can use a PropertyAccessor to access a property named
 * 'markingId' is a valid subject or token.
 */
interface MarkableSubjectInterface {
    /**
     * Get the subject's markingId
     *
     * @return string markingId
     */
    public function getMarkingId();

    /**
     * Set the subject's markingId
     *
     * @param
     */
    public function setMarkingId(string $markingId);
}
