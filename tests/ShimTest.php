<?php

namespace JBJ\Workflow\MarkingStore\Tests;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\Marking;
use Ramsey\Uuid\Validator\Validator as UuidValidator;
use JBJ\Workflow\MarkingStore\EventListener\InMemoryMarkingsListener;
use JBJ\Workflow\MarkingStore\InMemoryMarkings;
use JBJ\Workflow\MarkingStore\Mediator;
use JBJ\Workflow\MarkingStore\Shim;
use PHPUnit\Framework\TestCase;

class ShimTest extends TestCase
{
    public function testDefaults()
    {
        $mediator = new Mediator('test.mediator', 'test-property');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator);
        $validator = new UuidValidator();
        $this->assertTrue($validator->validate($shim->getName()));
        $this->assertInstanceOf(PropertyAccessorInterface::class, $shim->getPropertyAccessor());
        $this->assertEquals($mediator->getDefaultProperty(), $shim->getProperty());
    }

    public function testDefaultsWithPropertySeAtShim()
    {
        $mediator = new Mediator('test.mediator', 'masked-property');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator, 'test.shim', 'test-property');
        $this->assertEquals('test.shim', $shim->getName());
        $this->assertEquals('test-property', $shim->getProperty());
    }

    /** @expectedException \JBJ\Workflow\Exception\DomainException */
    public function testNoDispatcherSetOnMediatorThrows()
    {
        $mediator = new Mediator('test.mediator');
        new Shim($mediator);
    }

    protected function createSubject()
    {
        $subject = new class() {
            private $uuid;
            public function getTestProperty()
            {
                return $this->uuid;
            }
            public function setTestProperty(string $uuid)
            {
                $this->uuid = $uuid;
            }
        };
        return $subject;
    }

    protected function createReadOnlySubject(string $uuid = '')
    {
        $subject = new class($uuid) {
            private $uuid;
            public function __construct(string $uuid = '')
            {
                if (!empty($uuid)) {
                    $this->uuid = $uuid;
                }
            }
            public function getTestProperty()
            {
                return $this->uuid;
            }
        };
        return $subject;
    }

    protected function createBrokenSubject()
    {
        $subject = new class() {
        };
        return $subject;
    }

    public function testGetMarking()
    {
        $mediator = new Mediator('test.mediator', 'testProperty');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator);
        $subject = $this->createSubject();
        $marking = $shim->getMarking($subject);
        $this->assertInstanceOf(Marking::class, $marking);
        $this->assertEquals([], $marking->getPlaces());
        $this->assertNotNull($subject->getTestProperty());
    }

    /** @expectedException \JBJ\Workflow\Exception\DomainException */
    public function testGetMarkingThrowsIfSubjectIsArray()
    {
        $mediator = new Mediator('test.mediator', 'testProperty');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator);
        $subject = [];
        $shim->getMarking($subject);
    }

    /** @expectedException \JBJ\Workflow\Exception\InvalidArgumentException */
    public function testGetMarkingThrowsIfSubjectIsUnreadable()
    {
        $mediator = new Mediator('test.mediator', 'testProperty');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator);
        $subject = $this->createBrokenSubject();
        $shim->getMarking($subject);
    }

    /** @expectedException \JBJ\Workflow\Exception\InvalidArgumentException */
    public function testGetMarkingThrowsIfSubjectIsUnwritable()
    {
        $mediator = new Mediator('test.mediator', 'testProperty');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator);
        $subject = $this->createReadOnlySubject();
        $shim->getMarking($subject);
    }

    public function testGetMarkingOkIfSubjectIsUnwritableButHasUuidSet()
    {
        $mediator = new Mediator('test.mediator', 'testProperty');
        $mediator->setDispatcher(new EventDispatcher());
        $shim = new Shim($mediator);
        $subject = $this->createReadOnlySubject('be0daef6-5737-48f4-b3ad-9bd032637e2b');
        $marking = $shim->getMarking($subject);
        $this->assertEquals([], $marking->getPlaces());
    }

    protected function getLogger()
    {
        $logger = new class() implements LoggerInterface {
            use LoggerTrait;
            private $level;
            private $message;
            private $context;
            public function log($level, $message, array $context = [])
            {
                $this->level = $level;
                $this->message = $message;
                $this->context = $context;
            }
            public function getLevel()
            {
                return $this->level;
            }
            public function getMessage()
            {
                return $this->msg;
            }
            public function getContext()
            {
                return $this->context;
            }
        };
        return $logger;
    }

    public function testSetMarking()
    {
        $mediator = new Mediator('test.mediator', 'testProperty');
        $dispatcher = new EventDispatcher();
        $store = new InMemoryMarkings('test.markings');
        $logger = $this->getLogger();
        $storeDispatcher = new InMemoryMarkingsListener($logger, $store);
        $dispatcher->addSubscriber($storeDispatcher);
        $mediator->setDispatcher($dispatcher);
        $shim = new Shim($mediator);
        $subject = $this->createSubject();
        $this->assertNull($subject->getTestProperty());
        $expectedMarking = new Marking(['home' => 1, 'away' => 1]);
        $this->assertEquals($shim, $shim->setMarking($subject, $expectedMarking));
        $this->assertNotNull($subject->getTestProperty());
        $marking = $shim->getMarking($subject);
        $this->assertEquals($expectedMarking, $marking);
    }
}
