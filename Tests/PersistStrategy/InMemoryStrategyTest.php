<?php

namespace JBJ\Workflow\Tests\PersistStrategy;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\PersistStrategy\InMemoryStrategy;
use PHPUnit\Framework\TestCase;

class InMemoryStrategyTest extends TestCase
{
    public function testGetPlacesReturnEmptyArrayForMissingMarking()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $strategy = new InMemoryStrategy($logger);
        $this->assertEquals([], $strategy->getPlaces('store1', 'subject1'));
        return $strategy;
    }

    /**
     * @depends testGetPlacesReturnEmptyArrayForMissingMarking
     */
    public function testSetPlacesPersists($strategy)
    {
        $strategy->setPlaces('store1', 'subject1', ['place1', 'place2']);
        $places = $strategy->getPlaces('store1', 'subject1');
        $this->assertEquals(['place1', 'place2'], $places);
    }
}
