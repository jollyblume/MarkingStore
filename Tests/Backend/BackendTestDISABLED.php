<?php

namespace JBJ\Workflow\Tests\Backend;

use JBJ\Workflow\Backend\Backend;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit\Framework\TestCase;

class BackendTest extends TestCase
{
    private $dispatcher;
    protected function getDispatcher()
    {
        $dispatcher = $this->dispatcher;
        if (!$dispatcher) {
            $dispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());
            $this->dispatcher = $dispatcher;
        }
        return $dispatcher;
    }

    public function test
}
