<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Service\Category;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Service\Category\TreeService;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class TreeServiceTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new TreeService($this->createMock(FrontendInterface::class));
    }

    /**
     * @test
     */
    public function childrenRecursiveReturnAllChilderns(): void
    {
        $rootCat1 = TestsUtility::createEntity(Category::class, 12);
        $rootCat2 = TestsUtility::createEntity(Category::class, 22);

        $subCategories = TestsUtility::createMultipleEntities(Category::class, 5);

        $rootCat1->setSubCategories(
            TestsUtility::createObjectStorage($subCategories[0], $subCategories[1], $subCategories[2])
        );
        $rootCat2->setSubCategories(
            TestsUtility::createObjectStorage($subCategories[3], $subCategories[4], $subCategories[0])
        );

        $subCategory100 = TestsUtility::createEntity(Category::class, 100);
        $subCategories[0]->setSubCategories(TestsUtility::createObjectStorage($subCategory100));

        // UIDS
        $expect = [12, 1, 100, 2, 3, 22, 4, 5];

        self::assertEquals(
            $expect,
            TestsUtility::entitiesToUidsArray($this->subject->childrenRecursive([$rootCat1, $rootCat2]))
        );
    }

    /**
     * @test
     */
    public function fetchChildrenRecursiveIsNotCalledIfResultIsCache(): void
    {
        $cache = $this->prophesize(FrontendInterface::class);
        $cache->has('hash')->shouldBeCalled()->willReturn(true);
        $cache->get('hash')->shouldBeCalled()->willReturn([true]);

        $subject = $this->createPartialMock(TreeService::class, ['cacheHash', 'fetchChildrenRecursive']);
        $subject->expects(self::once())->method('cacheHash')->willReturn('hash');

        $subject->expects(self::never())->method('fetchChildrenRecursive');

        $this->inject($subject, 'cache', $cache->reveal());

        $subject->childrenIdsRecursiveAndCache([TestsUtility::createEntity(Category::class, 1)]);
    }

    /**
     * @test
     * @dataProvider validateTypeData
     * @param mixed $value
     * @param mixed $isValid
     */
    public function validateTypeExpectTraversableOrArray($value, $isValid): void
    {
        if ($isValid) {
            self::assertNull($this->callInaccessibleMethod($this->subject, 'validateType', $value));
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
                true,
            ],
            'valid_object_storage' => [
                new ObjectStorage(),
                true,
            ],
            'invalid' => [
                123,
                false,
            ],
        ];
    }
}
