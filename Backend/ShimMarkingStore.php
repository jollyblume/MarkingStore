<?php

namespace JBJ\Workflow\Backend;

/*
 * Forked from symfony/workflow
 */

use Symfony\Component\Workflow\Marking as BaseMarking;
use Symfony\Component\Workflow\Marking\MarkingStoreInterface as BaseMarkingStoreInterface;
use JBJ\Workflow\MarkingStoreInterface;
use JBJ\Workflow\MarkingInterface;
use JBJ\Workflow\Document\Marking;
use JBJ\Workflow\Traits\MarkingConverterTrait;
use JBJ\Common\Exception\PropImmutableException;
use JBJ\Common\Exception\OutOfScopeException;
use JBJ\Common\Exception\PropRequiredException;
use JBJ\Common\Traits\PropertyAccessorTrait;

/**
 * MultiTenantMarkingStore
 *
 * MultiTenantMarkingStore is forked from the symfony/workflow component and implements
 * the component's MarkingStoreInterface.
 *
 * This marking store maintains the marking for every subject participating in
 * this workflow. Marking stores are persisted to a MultiTenantMarkingStoreBackend.
 *
 * Each MarkingStore instance has a UUID, called 'storeId'. This is used
 * to uniquely identify the marking store and it's related workflow.
 *
 * Each subject (token) participating in any workflow will be injected with a
 * UUID, called 'markingId'. This will only be injected once and will uniquely
 * identify this subject (token) throughout the marking store backend.
 */
class ShimMarkingStore implements BaseMarkingStoreInterface, MarkingStoreInterface
{
    use PropertyAccessorTrait, MarkingConverterTrait { convertPlacesToKeys as public; }

    const MARKING_ID_PROPERTY = 'subectId';
    const MARKING_STORE_NAME = 'workflow.marking-store';
    const MARKING_NAME = 'workflow.marking';

    /**
     * Marking Store ID
     *
     * @var string $storeId
     */
    private $storeId;

    /**
     * Marking store backend
     *
     * @var ShimmedBackendInterface $backend
     */
    private $backend;

    public function __construct(ShimmedBackendInterface $backend, string $storeId = '')
    {
        $this->backend = $backend;
        if (!$storeId) {
            $storeId = $backend->createId(self::MARKING_STORE_NAME);
        }
        $this->storeId = $storeId;
    }

    // public function compareMarkings(BaseMarking $marking1, BaseMarking $marking2) {
    //     $markingsEqual = true;
    //     $isReadable1 = $this->isPropertyValueReadable($marking1, self::MARKING_ID_PROPERTY);
    //     $isReadable2 = $this->isPropertyValueReadable($marking2, self::MARKING_ID_PROPERTY);
    //     if ($isReadable1 && $isReadable2) {
    //         $markingId1 = $this->getPropertyValue($marking1, self::MARKING_ID_PROPERTY);
    //         $markingId2 = $this->getPropertyValue($marking2, self::MARKING_ID_PROPERTY);
    //         $markingsEqual = $markingId1 === $markingId2;
    //     }
    //     $places1 = array_keys($marking1->getPlaces());
    //     $places2 = array_keys($marking2->getPlaces());
    //     $count1 = count($places1);
    //     $count2 = count($places2);
    //     $count3 = count(array_intersect($places1, $places2));
    //     $placesEqual = ($count1 === $count2) && ($count2 === $count3);
    //     return $markingsEqual && $placesEqual;
    // }

    /**
    * Get this marking store id
    *
    * @return string storeId
    */
    public function getStoreId() :string
    {
        return $this->storeId;
    }

    protected function getBackend()
    {
        return $this->backend;
    }

    /**
     * Assert a subject is valid
     *
     * @throws OutOfScopeException
     */
    protected function assertValidSubject($subject)
    {
        $isReadable = $this->isPropertyValueReadable($subject, self::MARKING_ID_PROPERTY);
        if (!$isReadable) {
            throw new OutOfScopeException("Subject's markingId is not readable");
        }
    }

    /**
     * Get the markingId from a subject (token)
     *
     * Side effect:
     *  If the subject doesn't have a markingId, one will be created and applied.
     *
     * @param mixed $subject Must support MarkableSubjectInterface
     * @return string markingId
     * @throws OutOfScopeException Must support MarkableSubjectInterface
     *                             MarkableSubjectInterface does not have to be
     *                             implemented by the subject. The methods from
     *                             the interface must be implemented.
     */
    public function getMarkingId($subject)
    {
        $this->assertValidSubject($subject);
        $markingId = $this->getPropertyValue($subject, self::MARKING_ID_PROPERTY);
        if (empty($markingId)) {
            $markingId = $this->getBackend()->createId(self::MARKING_NAME);
            $this->setPropertyValue(
                $subject,
                self::MARKING_ID_PROPERTY,
                $markingId
            );
        }
        return $markingId;
    }

    /**
     * Get a subject's workflow marking for this marking store
     *
     * @param object $subject The subject or token
     * @return MarkingInterface The subject's marking within this marking store.
     *                     BaseMarking is the default when no marking exists for
     *                      the subject within this store.
     *                     Marking (inherited from BaseMarking) is returned when
     *                      results come from the backend.
     */
    public function getMarking($subject)
    {
        $storeId = $this->getStoreId();
        $markingId = $this->getMarkingId($subject);
        $backend = $this->getBackend();
        $marking = $backend->getMarking($storeId, $markingId);
        if (!$marking) {
            // todo bc:symfony/workflow marking different class
            return new BaseMarking();
        }
        $places = $marking->getPlaces();
        $marking = new BaseMarking($places);
        return $marking;
    }

    /**
     * Set the subject's workflow marking for this marking store
     *
     * @param string $subject The subject or token
     * @param MarkingInterface The subject's marking within this marking store.
     *                    The BaseMarking is converted to a Marking internally.
     * @return self
     */
    public function setMarking($subject, BaseMarkingInterface $marking)
    {
        $this->assertValidSubject($subject);
        $storeId = $this->getStoreId();
        $backend = $this->getBackend();
        $places = $marking->getPlaces();
        $marking = new Marking($markingId, $places);
        $backend->setMarking($storeId, $marking);
        return $this;
    }
}
