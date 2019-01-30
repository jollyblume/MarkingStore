<?php

namespace JBJ\Workflow\Tests\Document;

use PHPUnit\Framework\TestCase;
use JBJ\Workflow\Document\Marking;
use JBJ\Workflow\StoreCollectionInterface;
use JBJ\Workflow\MarkingStoreInterface;

class MarkingTest extends TestCase
{
    public function testConstructor()
    {
        $marking = new Marking('testId', ['testPlace']);
        $this->assertEquals('testId', $marking->getMarkingId());
        $this->assertEquals(['testPlace' => 1], $marking->getPlaces());
    }

    public function testGetMarkingStoreNullByDefault()
    {
        $marking = new Marking('testId');
        $this->assertNull($marking->getMarkingStore());
    }

    public function testSetMarkingStore()
    {
        $markingStore = new class() implements MarkingStoreInterface {
            public function getStoreId()
            {
                return 'testStore';
            }
            public function getStores()
            {
                return null;
            }
            public function setStores(StoreCollectionInterface $stores)
            {
            }
        };
        $marking = new Marking('testId');
        $marking->setMarkingStore($markingStore);
        $this->assertEquals($markingStore, $marking->getMarkingStore());
    }

    public function testUnmark()
    {
        $marking = new Marking('testId', ['testPlace']);
        $this->assertEquals(['testPlace' => 1], $marking->getPlaces());
        $this->assertTrue($marking->has('testPlace'));
        $marking->unmark('testPlace');
        $this->assertEquals([], $marking->getPlaces());
        $this->assertFalse($marking->has('testPlace'));
    }
}
