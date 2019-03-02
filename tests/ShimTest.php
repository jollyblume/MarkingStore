<?php

namespace JBJ\Workflow\MarkingStore\Tests;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\Marking;
use Ramsey\Uuid\Validator\Validator as UuidValidator;
use JBJ\Workflow\MarkingStore\Mediator;
use JBJ\Workflow\MarkingStore\Shim;
use PHPUnit\Framework\TestCase;

class ShimTest extends TestCase
{
    public function testDefaults()
    {
        $mediator = new Mediator('test.mediator', '[test-property]');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator);
        $validator = new UuidValidator();
        $this->assertTrue($validator->validate($shim->getName()));
        $this->assertInstanceOf(PropertyAccessorInterface::class, $shim->getPropertyAccessor());
        $this->assertEquals($mediator->getDefaultProperty(), $shim->getProperty());
    }

    public function testDefaultsWithPropertySet()
    {
        $mediator = new Mediator('test.mediator');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator, 'test.shim', '[test-property]');
        $this->assertEquals('test.shim', $shim->getName());
        $this->assertEquals('[test-property]', $shim->getProperty());
    }

    /** @expectedException \JBJ\Workflow\Exception\DomainException */
    public function testNoDispatcherSetOnMediatorThrows()
    {
        $mediator = new Mediator('test.mediator');
        new Shim($mediator);
    }

    public function testGetMarking()
    {
        $mediator = new Mediator('test.mediator', '[test-property]');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator);
        $subject = ['[test-property]' => null];
        $marking = $shim->getMarking($subject);
        $this->assertInstanceOf(Marking::class, $marking);
        $this->assertEquals([], $marking->getPlaces());
        $this->assertNotNull($subject['[test-property]']);
    }
}
