<?php

namespace JBJ\Workflow\Tests\MarkingStore;

use Symfony\Component\Workflow\Marking;
use Symfony\Component\EventDispatcher\EventDispatcher;
use JBJ\Workflow\Validator\UuidValidator;
use JBJ\Workflow\MarkingStore\MarkingStoreShim;
use PHPUnit\Framework\TestCase;

class MarkingStoreShimTest extends TestCase
{
    protected function createAcceptableSubject()
    {
        $subject = new class()
        {
            private $subjectId;
            public function getSubjectId()
            {
                return $this->subjectId;
            }
            public function setSubjectId($subjectId)
            {
                $this->subjectId = $subjectId;
            }
        };
        return $subject;
    }

    protected function createUnreadableSubject()
    {
        $subject = new class()
        {
            private $subjectId;
            public function setSubjectId($subjectId)
            {
                $this->subjectId = $subjectId;
            }
        };
        return $subject;
    }

    protected function createUnwritableSubject()
    {
        $subject = new class()
        {
            private $subjectId;
            public function getSubjectId()
            {
                return $this->subjectId;
            }
        };
        return $subject;
    }

    private $dispatcher;
    protected function getDispatcher() {
        $dispatcher = $this->dispatcher;
        if (null === $dispatcher) {
            $dispatcher = new EventDispatcher();
            $this->dispatcher = $dispatcher;
        }
        return $dispatcher;
    }

    /** @expectedException \JBJ\Workflow\Exception\FixMeException */
    public function testPropertyIsMarking()
    {
        $store = new MarkingStoreShim($this->getDispatcher(), null, 'marking');
    }

    public function testGetMarkingStoreId()
    {
        $validator = new UuidValidator();
        $store = new MarkingStoreShim($this->getDispatcher());
        $this->assertTrue($validator->validate($store->getMarkingStoreId()));
    }

    public function testGetMarkingSetsSubjectIdIfNotSet()
    {
        $store = new MarkingStoreShim($this->getDispatcher());
        $subject = $this->createAcceptableSubject();
        $marking = $store->getMarking($subject);
        $this->assertInstanceOf(Marking::class, $marking);
        $validator = new UuidValidator();
        $this->assertTrue($validator->validate($subject->getSubjectId()));
    }

    public function testGetMarkingNotSetSubjectIdIfSet()
    {
        $store = new MarkingStoreShim($this->getDispatcher());
        $subject = $this->createAcceptableSubject();
        $uuid = '6562eddd-227d-4b94-ba4c-70b94e4101c9';
        $subject->setSubjectId($uuid);
        $marking = $store->getMarking($subject);
        $this->assertEquals($uuid, $subject->getSubjectId());
    }

    public function testSetMarkingSetsSubjectIdIfNotSet()
    {
        $store = new MarkingStoreShim($this->getDispatcher());
        $subject = $this->createAcceptableSubject();
        $marking = new Marking();
        $store->setMarking($subject, $marking);
        $validator = new UuidValidator();
        $this->assertTrue($validator->validate($subject->getSubjectId()));
    }

    public function testSetMarkingNotSetSubjectIdIfSet()
    {
        $store = new MarkingStoreShim($this->getDispatcher());
        $subject = $this->createAcceptableSubject();
        $uuid = '6562eddd-227d-4b94-ba4c-70b94e4101c9';
        $subject->setSubjectId($uuid);
        $marking = new Marking();
        $store->setMarking($subject, $marking);
        $this->assertEquals($uuid, $subject->getSubjectId());
    }

    /**
     * @expectedException \JBJ\Workflow\Exception\FixMeException
     */
    public function testGetMarkingThrowsIfSubjectNotReadable()
    {
        $store = new MarkingStoreShim($this->getDispatcher());
        $subject = $this->createUnreadableSubject();
        $marking = $store->getMarking($subject);
    }

    /**
     * @expectedException \JBJ\Workflow\Exception\FixMeException
     */
    public function testGetMarkingThrowsIfSubjectNotWritable()
    {
        $store = new MarkingStoreShim($this->getDispatcher());
        $subject = $this->createUnwritableSubject();
        $marking = $store->getMarking($subject);
    }
}
