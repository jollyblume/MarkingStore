<?php

namespace JBJ\Workflow\Tests\EventListener;

use Psr\Log\LoggerInterface;
use JBJ\Workflow\Event\WorkflowEvent;
use JBJ\Workflow\EventListener\PersistListener;
use JBJ\Workflow\PersistStrategy\InMemoryStrategy;
use PHPUnit\Framework\TestCase;

class PersistListenerTest extends TestCase
{
    public function testOnGet()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $strategy = new InMemoryStrategy($logger);
        $listener = new PersistListener($logger, $strategy);
        $event = new WorkflowEvent('store1', 'subject1');
        $this->assertEquals([], $listener->onGet($event));
    }
}
