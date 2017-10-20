<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Navigation;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Navigation\CategoriesNavigationTreeBuilder;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
        unset($this->mockedNavigationBuilder);
    }
}
