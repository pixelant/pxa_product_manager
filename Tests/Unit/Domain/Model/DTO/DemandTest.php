<?php

namespace Pixelant\PxaProductManager\Tests\Unit\DTO;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Class DemandTest
 * @package Pixelant\PxaProductManager\Tests\Unit\DTO
 */
class DemandTest extends UnitTestCase
{
    /**
     * @var Demand
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Demand();
    }

    /**
     * @test
     */
    public function limitCanBeSet()
    {
        // default value
        $this->assertEquals(
            0,
            $this->fixture->getLimit()
        );
        $limit = 5;
        $this->fixture->setLimit($limit);

        $this->assertEquals(
            $limit,
            $this->fixture->getLimit()
        );
    }

    /**
     * @test
     */
    public function offSetCanBeSet()
    {
        // default value
        $this->assertEquals(
            0,
            $this->fixture->getOffSet()
        );

        $offeSet = 2;
        $this->fixture->setOffSet($offeSet);

        $this->assertEquals(
            $offeSet,
            $this->fixture->getOffSet()
        );
    }

    /**
     * @test
     */
    public function storagePidCanBeSet()
    {
        // default value
        $this->assertEmpty($this->fixture->getStoragePid());

        $storagePid = [5];
        $this->fixture->setStoragePid($storagePid);

        $this->assertEquals(
            $storagePid,
            $this->fixture->getStoragePid()
        );
    }

    /**
     * @test
     */
    public function orderByCanBeSet()
    {
        // default value
        $this->assertEquals(
            'name',
            $this->fixture->getOrderBy()
        );

        $orderBy = 'test';
        $this->fixture->setOrderBy($orderBy);

        $this->assertEquals(
            $orderBy,
            $this->fixture->getOrderBy()
        );
    }

    /**
     * @test
     */
    public function orderDirectionCanBeSet()
    {
        // default value
        $this->assertEquals(
            QueryInterface::ORDER_DESCENDING,
            $this->fixture->getOrderDirection()
        );

        $orderDirection = QueryInterface::ORDER_ASCENDING;
        $this->fixture->setOrderDirection($orderDirection);

        $this->assertEquals(
            $orderDirection,
            $this->fixture->getOrderDirection()
        );
    }

    /**
     * @test
     */
    public function orderByAllowedCanBeSet()
    {
        // default value
        $this->assertEmpty($this->fixture->getOrderByAllowed());

        $orderByAllowed = 'name,test';
        $this->fixture->setOrderByAllowed($orderByAllowed);

        $this->assertEquals(
            $orderByAllowed,
            $this->fixture->getOrderByAllowed()
        );
    }

    /**
     * @test
     */
    public function categoriesCanBeSet()
    {
        // default value
        $this->assertEmpty($this->fixture->getCategories());

        $categories = [123, 321];
        $this->fixture->setCategories($categories);

        $this->assertEquals(
            $categories,
            $this->fixture->getCategories()
        );
    }

    /**
     * @test
     */
    public function filtersCanBeSet()
    {
        // default value
        $this->assertEmpty($this->fixture->getFilters());

        $filters = [123, 321];
        $this->fixture->setFilters($filters);

        $this->assertEquals(
            $filters,
            $this->fixture->getFilters()
        );
    }

    /**
     * @test
     */
    public function filtersConjunctionCanBeSet()
    {
        // default value
        $this->assertEquals(
            'and',
            $this->fixture->getFiltersConjunction()
        );

        $filtersConjunction = 'or';
        $this->fixture->setFiltersConjunction($filtersConjunction);

        $this->assertEquals(
            $filtersConjunction,
            $this->fixture->getFiltersConjunction()
        );
    }

    /**
     * @test
     */
    public function categoryConjunctionCanBeSet()
    {
        // default value
        $this->assertEquals(
            'or',
            $this->fixture->getCategoryConjunction()
        );

        $categoryConjunction = 'and';
        $this->fixture->setCategoryConjunction($categoryConjunction);

        $this->assertEquals(
            $categoryConjunction,
            $this->fixture->getCategoryConjunction()
        );
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }
}
