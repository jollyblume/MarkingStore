<?php

namespace JBJ\Workflow;

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Workflow as BaseWorkflow;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use JBJ\Workflow\MarkingStore\MarkingStoreShim;
use JBJ\Workflow\Traits\CreateIdTrait;

class Workflow extends BaseWorkflow
{
    use CreateIdTrait;

    private $parent;

    public function __construct(Definition $definition, EventDispatcherInterface $dispatcher = null, PropertyAccessorInterface $propertyAccessor = null)
    {
        $propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        $metadata = $definition->getMetadataStore()->getWorkflowMetadata()
        if (array_key_exists('name', $metadata)) {
            $name = $metadata['name'];
        }
        $name = $this->createId($name); // ensure $name is a uuid
        $markingStore = new MarkingStoreShim($dispatcher, $propertyAccessor, 'subjectId', $name);
        parent::__construct($definition, $markingStore, $dispatcher, $name);
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        //todo input testing
        $this->parent = $parent;
    }
}
