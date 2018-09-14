<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Navigation;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Navigation\CategoriesNavigationTreeBuilder;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class CategoriesNavigationTreeBuilderTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Navigation
 */
class CategoriesNavigationTreeBuilderTest extends UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface|CategoriesNavigationTreeBuilder
     */
    protected $mockedNavigationBuilder;

    protected function setUp()
    {
        $this->mockedNavigationBuilder = $this->getAccessibleMock(
            CategoriesNavigationTreeBuilder::class,
            ['dummy'],
            [],
            '',
            false,
            false
        );

        $tsfe = new \stdClass();
        $GLOBALS['TSFE'] = $tsfe;
    }

    /**
     * @test
     */
    public function buildingTreeWithHighLevelThrowException()
    {
        $queryResult = $this->createMock(QueryResultInterface::class);
        $result = [];
        $activeCategory = 0;
        $level = 51;

        $this->expectException(\RuntimeException::class);
        $this->mockedNavigationBuilder->_callRef('buildDeepTree', $queryResult, $activeCategory, $result, $level);
    }

    /**
     * @test
     */
    public function findSubCategoriesThrowExceptionIfParentMetTwoTimes()
    {
        $parentCategories = [1, 22, 33];
        $this->mockedNavigationBuilder->_set('parentCategoriesUids', $parentCategories);

        $parentCategory = new Category();
        $parentCategory->_setProperty('uid', 22);

        $this->expectException(\RuntimeException::class);
        $this->assertNull($this->mockedNavigationBuilder->_call('findSubCategories', $parentCategory));
    }

    /**
     * @test
     */
    public function defaultActiveListisEmptyArray()
    {
        $this->assertEquals(
            [],
            $this->mockedNavigationBuilder->_get('activeList')
        );
    }

    /**
     * @test
     */
    public function excludeCategoriesCanBeSet()
    {
        $excludeCategories = [1, 2, 3];

        $this->mockedNavigationBuilder->setExcludeCategories($excludeCategories);

        $this->assertEquals(
            $excludeCategories,
            $this->mockedNavigationBuilder->getExcludeCategories()
        );
    }

    /**
     * @test
     */
    public function expandAllCanBeSet()
    {
        $this->mockedNavigationBuilder->setExpandAll(true);

        $this->assertTrue($this->mockedNavigationBuilder->isExpandAll());
    }

    /**
     * @test
     */
    public function defaultSortringIsAscAndBySortingField()
    {
        $defaultSorting = ['sorting' => QueryInterface::ORDER_ASCENDING];

        $this->assertEquals(
            $defaultSorting,
            $this->mockedNavigationBuilder->getOrderings()
        );
    }

    /**
     * @test
     */
    public function sortingCanBeSet()
    {
        $sorting = ['name' => QueryInterface::ORDER_DESCENDING];

        $this->mockedNavigationBuilder->setOrderings($sorting);

        $this->assertEquals(
            $sorting,
            $this->mockedNavigationBuilder->getOrderings()
        );
    }

    protected function tearDown()
    {
        unset($this->mockedNavigationBuilder, $GLOBALS['TSFE']);
    }
}
