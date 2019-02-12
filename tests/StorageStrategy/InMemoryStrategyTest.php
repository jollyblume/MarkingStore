<?php

namespace JBJ\Workflow\MarkingStore\Tests\StorageStrategy;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\MarkingStore\StorageStrategy\InMemoryStrategy;
use PHPUnit\Framework\TestCase;

class InMemoryStrategyTest extends TestCase
{
    public function testGetPlacesReturnEmptyArrayForMissingMarking()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $strategy = new InMemoryStrategy($logger);
        $this->assertEquals([], $strategy->getPlaces('store1', 'subject1'));
        return $strategy;
    }

    /**
     * @depends testGetPlacesReturnEmptyArrayForMissingMarking
     */
    public function testSetPlacesStorages($strategy)
    {
        $strategy->setPlaces('store1', 'subject1', ['place1', 'place2']);
        $places = $strategy->getPlaces('store1', 'subject1');
        $this->assertEquals(['place1', 'place2'], $places);
        return $strategy;
    }

    /**
     * @depends testSetPlacesStorages
     */
    public function testSetPlacesStorageEmptyPlacesStillGetsDefault($strategy)
    {
        $strategy->setPlaces('store1', 'subject1', []);
        $places = $strategy->getPlaces('store1', 'subject1');
        $this->assertEquals([], $places);
        return $strategy;
    }
}
