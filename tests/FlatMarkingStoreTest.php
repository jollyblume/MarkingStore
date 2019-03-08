<?php

namespace JBJ\Workflow\Tests\MarkingStore;

use JBJ\Workflow\MarkingStore\FlatMarkingStore;
use JBJ\Workflow\MarkingStore\Marking;
use PHPUnit\Framework\TestCase;

class FlatMarkingStoreTest extends TestCase
{
    public function testDefaults()
    {
        $markings = new FlatMarkingStore();
        $marking = new Marking('store', 'property', '$subjectUuid');
        $this->assertEquals($marking, $markings->getMarking('store', 'property', '$subjectUuid'));
    }

    public function testSetMarking()
    {
        $markings = new FlatMarkingStore();
        $marking = new Marking('store', 'property', '$subjectUuid', 'mars');
        $this->assertEquals($markings, $markings->setMarking($marking));
        $this->assertEquals($marking, $markings->getMarking('store', 'property', '$subjectUuid'));
    }

    public function testSetMarkingEmpty()
    {
        $markings = new FlatMarkingStore();
        $marking = new Marking('store', 'property', '$subjectUuid');
        $this->assertEquals($marking, $markings->getMarking('store', 'property', '$subjectUuid'));
    }
}
