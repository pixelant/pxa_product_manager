<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Collection;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Arrayable;
use Pixelant\PxaProductManager\Domain\Collection\Collection;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use TYPO3\CMS\Extbase\Domain\Model\Category;

class CollectionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function collectionThrownExceptionIfNotIterableArgumentGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Collection('test');
    }

    /**
     * @test
     */
    public function canMapCollectionWithKeysFromPropertyValue(): void
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

        self::assertEquals($expect, $collection->mapWithKeysOfProperty('uid')->toArray());
    }

    /**
     * @test
     */
    public function canMapCollectionWithKeysFromPropertyValueWithCallbackFunction(): void
    {
        $item1 = [
            'prop' => createEntity(Category::class, 22),
            'name' => 'test',
        ];
        $item2 = [
            'prop' => createEntity(Category::class, 33),
            'name' => 'name test',
        ];
        $collectionArray = [
            $item1,
            $item2,
        ];

        $expect = [22 => $item1, 33 => $item2];

        $collection = new Collection($collectionArray);

        self::assertEquals($expect, $collection->mapWithKeysOfProperty('prop', fn ($collectionKey) => $collectionKey->getUid())->toArray());
    }

    /**
     * @test
     * @dataProvider toArrayReturnIterableToArrayDataProvider
     * @param mixed $items
     * @param mixed $expect
     */
    public function toArrayReturnIterableToArray($items, $expect): void
    {
        $collectionInstance = new Collection($items);

        self::assertEquals($expect, array_values($collectionInstance->toArray()));
    }

    /**
     * @test
     */
    public function pluckExtractValuesByPropertyFromArray(): void
    {
        $items = [
            ['uid' => 1, 'title' => 'test me'],
            createEntity(Category::class, ['uid' => 123, 'title' => 'Im category']),
        ];

        $collection = new Collection($items);
        $expect = [1, 123];

        self::assertEquals($expect, $collection->pluck('uid')->toArray());
    }

    /**
     * @test
     */
    public function pluckExtractValuesByPropertyFromObjectStorage(): void
    {
        $items = createObjectStorage(
            createEntity(Category::class, 88),
            createEntity(Category::class, 99),
        );

        $collection = new Collection($items);
        $expectFromStorage = [88, 99];

        self::assertEquals($expectFromStorage, array_values($collection->pluck('uid')->toArray()));
    }

    /**
     * @test
     */
    public function pluckWithCallbackWillApplyCallbackFunctionToKeys(): void
    {
        $attribute1 = createEntity(Attribute::class, 5);
        $attribute2 = createEntity(Attribute::class, 10);

        $attributeValue1 = createEntity(AttributeValue::class, ['uid' => 1, 'attribute' => $attribute1]);
        $attributeValue2 = createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute2]);

        $items = [
            $attributeValue1,
            $attributeValue2,
        ];

        $expectAttributesUids = [5, 10];
        $collection = new Collection($items);

        self::assertEquals(
            $expectAttributesUids,
            $collection->pluck('attribute', fn ($attribute) => $attribute->getUid())->toArray()
        );
    }

    /**
     * @test
     */
    public function unionUniquePropertyWillAddOnlyUniqueItems(): void
    {
        $item1 = ['uid' => 1, 'title' => 'I\'m title'];
        $category1 = createEntity(Category::class, ['uid' => 123, 'title' => 'Im category']);

        $item23 = ['uid' => 23, 'title' => 'Test title'];
        $category445 = createEntity(Category::class, ['uid' => 445, 'title' => 'In collection']);

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

        self::assertEquals($expect, array_values($merged->toArray()));
    }

    /**
     * @test
     */
    public function shiftLevelWillRemoveFirstLevelOfArray(): void
    {
        $item1 = ['uid' => 1];
        $item2 = ['uid' => 2];
        $item3 = ['uid' => 2];

        $items = [
            [$item1, $item2],
            [$item3],
        ];

        $expect = [$item1, $item2, $item3];
        $collection = new Collection($items);

        self::assertEquals($expect, $collection->shiftLevel()->toArray());
    }

    /**
     * @test
     */
    public function shiftLevelWillRemoveFirstLevelOfObjectStorage(): void
    {
        $item1 = createEntity(Category::class, 1);
        $item2 = createEntity(Category::class, 2);
        $item3 = createEntity(Category::class, 3);

        $items = [
            createObjectStorage($item2, $item3),
            createObjectStorage($item1),
        ];

        $expect = [$item2, $item3, $item1];
        $collection = new Collection($items);

        self::assertEquals($expect, array_values($collection->shiftLevel()->toArray()));
    }

    /**
     * @test
     */
    public function uniqueWillReturnArrayOfUniqueValues(): void
    {
        [$item1, $item2, $item3] = createMultipleEntities(Category::class, 3);
        $items = [$item1, $item2, $item3, $item2, $item3];

        $expect = [$item1, $item2, $item3];
        $collection = new Collection($items);

        self::assertEquals($expect, $collection->unique()->toArray());
    }

    /**
     * @test
     */
    public function searchOneByPropertyWillReturnNullIfNothingIsFound(): void
    {
        [$item1, $item2, $item3] = createMultipleEntities(Category::class, 3);
        $items = [$item1, $item2, $item3];

        $collection = new Collection($items);

        self::assertNull($collection->searchOneByProperty('uid', 5));
    }

    /**
     * @test
     */
    public function searchOneByPropertyWillReturnItemIfFound(): void
    {
        [$item1, $item2, $item3] = createMultipleEntities(Category::class, 3);
        $items = [$item1, $item2, $item3];

        $collection = new Collection($items);

        self::assertSame($item2, $collection->searchOneByProperty('uid', 2));
    }

    /**
     * @test
     */
    public function searchOneByPropertyWithCallbackWillReturnItemIfFound(): void
    {
        $attribute1 = createEntity(Attribute::class, 10);
        $attribute2 = createEntity(Attribute::class, 50);
        $attribute3 = createEntity(Attribute::class, 100);

        $attributeValue1 = createEntity(AttributeValue::class, ['uid' => 1, 'attribute' => $attribute1]);
        $attributeValue2 = createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute2]);
        $attributeValue3 = createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute3]);

        $items = [
            $attributeValue1,
            $attributeValue2,
            $attributeValue3,
        ];

        $collection = new Collection($items);

        self::assertSame(
            $attributeValue2,
            $collection->searchOneByProperty('attribute', 50, fn ($attribute) => $attribute->getUid())
        );
    }

    /**
     * @test
     */
    public function searchByPropertyWillReturnItems(): void
    {
        $attribute1 = createEntity(Attribute::class, 10);
        $attribute2 = createEntity(Attribute::class, 50);

        $attributeValue1 = createEntity(AttributeValue::class, ['uid' => 1, 'attribute' => $attribute1]);
        $attributeValue2 = createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute2]);
        $attributeValue3 = createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute2]);

        $items = [
            $attributeValue1,
            $attributeValue2,
            $attributeValue3,
        ];

        $collection = new Collection($items);
        $expect = [$attributeValue2, $attributeValue3];

        self::assertEquals(
            $expect,
            $collection->searchByProperty('attribute', 50, fn ($attribute) => $attribute->getUid())->toArray()
        );
    }

    /**
     * @test
     */
    public function firstReturnFirstItemFromCollection(): void
    {
        [$item1, $item2, $item3] = createMultipleEntities(Category::class, 3);
        $items = [$item1, $item2, $item3];

        $collection = new Collection($items);

        self::assertSame($item1, $collection->first());
    }

    /**
     * @test
     */
    public function filterFilterItems(): void
    {
        [$item1, $item2, $item3] = createMultipleEntities(Category::class, 3);
        $items = [$item1, $item2, $item3];

        $collection = new Collection($items);

        self::assertEquals([$item1], $collection->filter(fn ($filterItem) => $filterItem->getUid() === 1)->toArray());
    }

    /**
     * @test
     */
    public function unshiftAddItemsToBeginningOfCollection(): void
    {
        [$item1, $item2, $item3] = createMultipleEntities(Category::class, 3);
        $items = [$item1, $item2, $item3];

        $item10 = createEntity(Category::class, 10);
        $item20 = createEntity(Category::class, 20);
        $add = [$item10, $item20];

        $collection = new Collection($items);

        $expect = [$item10, $item20, $item1, $item2, $item3];

        self::assertEquals($expect, $collection->unshift(...$add)->toArray());
    }

    /**
     * @test
     *
     * @dataProvider sortList
     * @param mixed $sortList
     */
    public function sortByKeysListSortCollectionByGivenList($sortList): void
    {
        $items = [
            ['title' => 'last', 'uid' => 10],
            ['title' => 'first', 'uid' => 5],
            ['title' => 'middle', 'uid' => 7],
        ];

        $collection = new Collection($items);
        $expect = [
            ['title' => 'first', 'uid' => 5],
            ['title' => 'middle', 'uid' => 7],
            ['title' => 'last', 'uid' => 10],
        ];

        self::assertEquals($expect, $collection->sortByOrderList($sortList, 'uid')->toArray());
    }

    public function sortList()
    {
        return [
            'string_list' => [
                'list' => '5, 7, 10',
            ],
            'array_list' => [
                'list' => ['5', '7', '10'],
            ],
        ];
    }

    public function toArrayReturnIterableToArrayDataProvider()
    {
        $items = [
            createEntity(Category::class, 1),
            createEntity(Category::class, 2),
        ];

        return [
            'object_storage_to_array' => [
                'object_storage' => createObjectStorage(...$items),
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
