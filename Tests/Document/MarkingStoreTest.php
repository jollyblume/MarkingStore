<?php

namespace JBJ\Workflow\Tests\Document;

use JBJ\Common\Tests\Collection\ShimBaseArrayCollectionTest;
use JBJ\Workflow\Document\MarkingStore;
use JBJ\Workflow\MarkingInterface;

class MarkingStoreTest extends ShimBaseArrayCollectionTest
{
    public function setUp()
    {
        $this->initCollectionBuilder(MarkingStore::class);
        $rules = [
            'name' => [
                'markingId',
                'isDisabled' => false,
                'isValid' => true,
            ],
            'parent' => [
                'markingStore',
                'isDisabled' => false,
                'isValid' => true,
            ],
        ];
        $this->initDataProvider($rules, true, true);
    }

    protected function buildAcceptableElement(string $key)
    {
        $element = new class($key) {
            private $markingId;
            private $markingStore;
            private $otherValue;
            public function __construct(string $key)
            {
                $this->markingId = $key;
            }
            public function getMarkingId()
            {
                return $this->markingId;
            }
            public function getMarkingStore()
            {
                return $this->markingStore;
            }
            public function setMarkingStore(?MarkingStore $markingStore)
            {
                $this->markingStore = $markingStore;
            }
            public function getOtherValue()
            {
                return $this->otherValue;
            }
            public function setOtherValue($otherValue)
            {
                $this->otherValue = $otherValue;
            }
        };
        return $element;
    }

    protected function createCollection(string $testClass, array $elements = [])
    {
        $collection = new $testClass('testMarkingStore', $elements);
        return $collection;
    }
}
