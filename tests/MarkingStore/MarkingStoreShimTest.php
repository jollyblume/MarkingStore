<?php

namespace JBJ\Workflow\MarkingStore\Tests\MarkingStore;

use Symfony\Component\Workflow\Marking;
use Symfony\Component\EventDispatcher\EventDispatcher;
use JBJ\Workflow\Validator\UuidValidator;
use JBJ\Workflow\MarkingStore\MarkingStore\MarkingStoreShim;
use PHPUnit\Framework\TestCase;

class MarkingStoreShimTest extends TestCase
{
    protected function createAcceptableSubject()
    {
        $subject = new class() {
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
        $subject = new class() {
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
        $subject = new class() {
            private $subjectId;
            public function getSubjectId()
            {
                return $this->subjectId;
            }
        };
        return $subject;
    }

    private $dispatcher;
    protected function getDispatcher()
    {
        $dispatcher = $this->dispatcher;
        if (null === $dispatcher) {
            $dispatcher = new EventDispatcher();
            $this->dispatcher = $dispatcher;
        }
        return $dispatcher;
    }

    private $propertyAccessor;
    protected function getPropertyAccessor()
    {
        $propertyAccessor = $this->propertyAccessor;
        if (null === $propertyAccessor) {
        }
    }

    protected function getMarkingStoreShim(string $property = 'subjectId', string $name = '')
    {
        $dispatcher = $this->getDispatcher();
        $propertyAccessor = $this->getPropertyAccessor();
        $store = new MarkingStoreShim($dispatcher, $propertyAccessor, $property, $name);
        return $store;
    }

    /** @expectedException \JBJ\Workflow\Exception\FixMeException */
    public function testPropertyIsMarking()
    {
        $this->getMarkingStoreShim('marking');
    }

    public function testGetMarkingStoreId()
    {
        $validator = new UuidValidator();
        $store = $this->getMarkingStoreShim();
        $this->assertTrue($validator->validate($store->getMarkingStoreId()));
    }

    public function testGetMarkingSetsSubjectIdIfNotSet()
    {
        $store = $this->getMarkingStoreShim();
        $subject = $this->createAcceptableSubject();
        $marking = $store->getMarking($subject);
        $this->assertInstanceOf(Marking::class, $marking);
        $validator = new UuidValidator();
        $this->assertTrue($validator->validate($subject->getSubjectId()));
    }

    public function testGetMarkingNotSetSubjectIdIfSet()
    {
        $store = $this->getMarkingStoreShim();
        $subject = $this->createAcceptableSubject();
        $uuid = '6562eddd-227d-4b94-ba4c-70b94e4101c9';
        $subject->setSubjectId($uuid);
        $marking = $store->getMarking($subject);
        $this->assertEquals($uuid, $subject->getSubjectId());
    }

    public function testSetMarkingSetsSubjectIdIfNotSet()
    {
        $store = $this->getMarkingStoreShim();
        $subject = $this->createAcceptableSubject();
        $marking = new Marking();
        $store->setMarking($subject, $marking);
        $validator = new UuidValidator();
        $this->assertTrue($validator->validate($subject->getSubjectId()));
    }

    public function testSetMarkingNotSetSubjectIdIfSet()
    {
        $store = $this->getMarkingStoreShim();
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
        $store = $this->getMarkingStoreShim();
        $subject = $this->createUnreadableSubject();
        $marking = $store->getMarking($subject);
    }

    /**
     * @expectedException \JBJ\Workflow\Exception\FixMeException
     */
    public function testGetMarkingThrowsIfSubjectNotWritable()
    {
        $store = $this->getMarkingStoreShim();
        $subject = $this->createUnwritableSubject();
        $marking = $store->getMarking($subject);
    }
}
