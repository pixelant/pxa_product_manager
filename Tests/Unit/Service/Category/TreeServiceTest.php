<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Service\Category;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Service\Category\TreeService;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Service
 */
class TreeServiceTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new TreeService($this->createMock(FrontendInterface::class));
    }

    /**
     * @test
     */
    public function childrenRecursiveReturnAllChilderns()
    {
        $rootCat1 = createEntity(Category::class, 12);
        $rootCat2 = createEntity(Category::class, 22);

        $subCategories = createMultipleEntities(Category::class, 5);

        $rootCat1->setSubCategories(createObjectStorage($subCategories[0], $subCategories[1], $subCategories[2]));
        $rootCat2->setSubCategories(createObjectStorage($subCategories[3], $subCategories[4], $subCategories[0]));

        $subCategory100 = createEntity(Category::class, 100);
        $subCategories[0]->setSubCategories(createObjectStorage($subCategory100));

        // UIDS
        $expect = [12, 1, 100, 2, 3, 22, 4, 5];

        $this->assertEquals($expect, entitiesToUidsArray($this->subject->childrenRecursive([$rootCat1, $rootCat2])));
    }

    /**
     * @test
     */
    public function fetchChildrenRecursiveIsNotCalledIfResultIsCache()
    {
        $cache = $this->prophesize(FrontendInterface::class);
        $cache->has('hash')->shouldBeCalled()->willReturn(true);
        $cache->get('hash')->shouldBeCalled()->willReturn([true]);

        $subject = $this->createPartialMock(TreeService::class, ['cacheHash', 'fetchChildrenRecursive']);
        $subject->expects($this->once())->method('cacheHash')->willReturn('hash');

        $subject->expects($this->never())->method('fetchChildrenRecursive');

        $this->inject($subject, 'cache', $cache->reveal());

        $subject->childrenRecursive([createEntity(Category::class, 1)]);
    }

    /**
     * @test
     * @dataProvider validateTypeData
     */
    public function validateTypeExpectTraversableOrArray($value, $isValid)
    {
        if ($isValid) {
            $this->assertNull($this->callInaccessibleMethod($this->subject, 'validateType', $value));
        } else {
            $this->expectException(\InvalidArgumentException::class);
            $this->callInaccessibleMethod($this->subject, 'validateType', $value);

        }
    }

    public function validateTypeData()
    {
        return [
            'valid_array' => [
                [],
                true
            ],
            'valid_object_storage' => [
                new ObjectStorage(),
                true,
            ],
            'invalid' => [
                123,
                false
            ]
        ];
    }
}
