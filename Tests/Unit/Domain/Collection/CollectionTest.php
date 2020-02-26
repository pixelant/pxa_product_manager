<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Collection;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Arrayable;
use Pixelant\PxaProductManager\Domain\Collection\Collection;
use TYPO3\CMS\Extbase\Domain\Model\Category;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Collection
 */
class CollectionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function collectionThrownExceptionIfNotIterableArgumentGiven()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Collection('test');
    }

    /**
     * @test
     */
    public function canMapCollectionWithKeysFromPropertyValue()
    {
        $collectionArray = [
            [
                'uid' => 123,
                'name' => 'test',
            ],
            [
                'uid' => 33,
                'name' => 'name test',
            ],
        ];

        $expect = array_combine(array_column($collectionArray, 'uid'), $collectionArray);

        $collection = new Collection($collectionArray);

        $this->assertEquals($expect, $collection->mapWithKeysOfProperty('uid')->toArray());
    }

    /**
     * @test
     * @dataProvider toArrayReturnIterableToArrayDataProvider
     */
    public function toArrayReturnIterableToArray($items, $expect)
    {
        $collectionInstance = new Collection($items);

        $this->assertEquals($expect, array_values($collectionInstance->toArray()));
    }

    /**
     * @test
     */
    public function pluckExtractValuesByPropertyFromArray()
    {
        $items = [
            ['uid' => 1, 'title' => 'test me'],
            createDomainInstanceWithProperties(Category::class, ['uid' => 123, 'title' => 'Im category']),
        ];

        $collection = new Collection($items);
        $expect = [1, 123];

        $this->assertEquals($expect, $collection->pluck('uid')->toArray());
    }

    /**
     * @test
     */
    public function pluckExtractValuesByPropertyFromObjectStorage()
    {
        $items = createObjectStorageWithObjects(
            createDomainInstanceWithProperties(Category::class, 88),
            createDomainInstanceWithProperties(Category::class, 99),
        );

        $collection = new Collection($items);
        $expectFromStorage = [88, 99];

        $this->assertEquals($expectFromStorage, array_values($collection->pluck('uid')->toArray()));
    }

    /**
     * @test
     */
    public function unionUniquePropertyWillAddOnlyUniqueItems()
    {
        $item1 = ['uid' => 1, 'title' => 'I\'m title'];
        $category1 = createDomainInstanceWithProperties(Category::class, ['uid' => 123, 'title' => 'Im category']);

        $item23 = ['uid' => 23, 'title' => 'Test title'];
        $category445 = createDomainInstanceWithProperties(Category::class, ['uid' => 445, 'title' => 'In collection']);

        $items = [
            $item1,
            $category1,
        ];

        $merge = [
            $item23,
            $item1,
            $category1,
            $category445,
        ];

        $expect = [$item1, $category1, $item23, $category445];

        $merged = (new Collection($items))->unionUniqueProperty($merge, 'uid');

        $this->assertEquals($expect, array_values($merged->toArray()));
    }

    /**
     * @test
     */
    public function shiftLevelWillRemoveFirstLevelOfArray()
    {
        $item1 = ['uid' => 1];
        $item2 = ['uid' => 2];
        $item3 = ['uid' => 2];

        $items = [
            [$item1, $item2],
            [$item3]
        ];

        $expect = [$item1, $item2, $item3];
        $collection = new Collection($items);

        $this->assertEquals($expect, $collection->shiftLevel()->toArray());
    }

    /**
     * @test
     */
    public function shiftLevelWillRemoveFirstLevelOfObjectStorage()
    {
        $item1 = createDomainInstanceWithProperties(Category::class, 1);
        $item2 = createDomainInstanceWithProperties(Category::class, 2);
        $item3 = createDomainInstanceWithProperties(Category::class, 3);

        $items = [
            createObjectStorageWithObjects($item2, $item3),
            createObjectStorageWithObjects($item1),
        ];

        $expect = [$item2, $item3, $item1];
        $collection = new Collection($items);

        $this->assertEquals($expect, array_values($collection->shiftLevel()->toArray()));
    }

    public function toArrayReturnIterableToArrayDataProvider()
    {
        $items = [
            createDomainInstanceWithProperties(Category::class, 1),
            createDomainInstanceWithProperties(Category::class, 2),
        ];

        return [
            'object_storage_to_array' => [
                'object_storage' => createObjectStorageWithObjects(...$items),
                'expect' => $items,
            ],
            'array_to_array' => [
                'array' => $items,
                'expect' => $items,
            ],
            'arrayable' => [
                'array_able' => new class($items) implements Arrayable {
                    protected $items;

                    public function __construct($items)
                    {
                        $this->items = $items;
                    }

                    public function toArray(): array
                    {
                        return $this->items;
                    }
                },
                'expect' => $items,
            ],
        ];
    }
}
