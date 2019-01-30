<?php

namespace JBJ\Workflow\Tests\MarkingStore;

use Symfony\Component\Workflow\Marking;
use JBJ\Workflow\BackendInterface;
use JBJ\Workflow\MarkingStore\MarkingStoreShim;
use PHPUnit\Framework\TestCase;

class MarkingStoreShimTest extends TestCase
{
    const UUID = 'daf3147e-46de-4dbe-bd6e-64f6bd6edc21';

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

    protected function createBackendMock(Marking $marking = null)
    {
        $backend = $this->getMockBuilder(BackendInterface::class)->getMock();
        $backend->method('getMarking')->willReturn($marking);
        $backend->method('createId')->willReturn(self::UUID);
        return $backend;
    }

    public function testGetMarkingStoreId()
    {
        $backend = $this->createBackendMock();
        $store = new MarkingStoreShim($backend);
        $this->assertEquals(self::UUID, $store->getMarkingStoreId());
    }

    public function testGetMarkingSetsSubjectIdIfNotSet()
    {
        $backend = $this->createBackendMock();
        $store = new MarkingStoreShim($backend);
        $subject = $this->createAcceptableSubject();
        $marking = $store->getMarking($subject);
        $this->assertInstanceOf(Marking::class, $marking);
        $this->assertEquals(self::UUID, $subject->getSubjectId());
    }

    public function testGetMarkingNotSetSubjectIdIfSet()
    {
        $backend = $this->createBackendMock();
        $store = new MarkingStoreShim($backend);
        $subject = $this->createAcceptableSubject();
        $uuid = '6562eddd-227d-4b94-ba4c-70b94e4101c9';
        $subject->setSubjectId($uuid);
        $marking = $store->getMarking($subject);
        $this->assertEquals($uuid, $subject->getSubjectId());
    }

    public function testSetMarkingSetsSubjectIdIfNotSet()
    {
        $backend = $this->createBackendMock();
        $store = new MarkingStoreShim($backend);
        $subject = $this->createAcceptableSubject();
        $marking = new Marking();
        $store->setMarking($subject, $marking);
        $this->assertEquals(self::UUID, $subject->getSubjectId());
    }

    public function testSetMarkingNotSetSubjectIdIfSet()
    {
        $backend = $this->createBackendMock();
        $store = new MarkingStoreShim($backend);
        $subject = $this->createAcceptableSubject();
        $uuid = '6562eddd-227d-4b94-ba4c-70b94e4101c9';
        $subject->setSubjectId($uuid);
        $marking = new Marking();
        $store->setMarking($subject, $marking);
        $this->assertEquals($uuid, $subject->getSubjectId());
    }

    /**
     * @expectedException \JBJ\Common\Exception\FixMeException
     */
    public function testGetMarkingThrowsIfSubjectNotReadable()
    {
        $backend = $this->createBackendMock();
        $store = new MarkingStoreShim($backend);
        $subject = $this->createUnreadableSubject();
        $marking = $store->getMarking($subject);
    }

    /**
     * @expectedException \JBJ\Common\Exception\FixMeException
     */
    public function testGetMarkingThrowsIfSubjectNotWritable()
    {
        $backend = $this->createBackendMock();
        $store = new MarkingStoreShim($backend);
        $subject = $this->createUnwritableSubject();
        $marking = $store->getMarking($subject);
    }
}
