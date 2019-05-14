<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\DTO;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\DTO\FiltersAvailableOptions;

/**
 * Class FiltersAvailableOptionsTest
 * @package Pixelant\PxaProductManager\Tests\Unit\DTO
 */
class FiltersAvailableOptionsTest extends UnitTestCase
{
    /**
     * @var FiltersAvailableOptions
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new FiltersAvailableOptions();
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->subject);
    }

    /**
     * @test
     */
    public function defaultAvailableCategoriesEmptyArray()
    {
        $this->assertCount(0, $this->subject->getAvailableCategories());
    }

    /**
     * @test
     */
    public function defaultAvailableAttributesEmptyArray()
    {
        $this->assertCount(0, $this->subject->getAvailableAttributes());
    }

    /**
     * @test
     */
    public function canSetAvailableCategories()
    {
        $avCats = [2, 4, 5];

        $this->subject->setAvailableCategories($avCats);

        $this->assertEquals($avCats, $this->subject->getAvailableCategories());
    }

    /**
     * @test
     */
    public function canSetAvailableAttributes()
    {
        $avAttributes = [21, 42, 54];

        $this->subject->setAvailableAttributes($avAttributes);

        $this->assertEquals($avAttributes, $this->subject->getAvailableAttributes());
    }

    /**
     * @test
     */
    public function setAvailableCategoriesForAllWillSetCategoriesWithAllKey()
    {
        $avCats = [33, 44, 55];

        $this->subject->setAvailableCategoriesForAll($avCats);

        $expect = [FiltersAvailableOptions::ALL_FILTERS_KEY => $avCats];
        $this->assertEquals($expect, $this->subject->getAvailableCategories());
    }

    /**
     * @test
     */
    public function setAvailableCategoriesForFilterWillSetCategoriesForFilter()
    {
        $filterUid = 100;
        $categories = [12, 99, 101];

        $expect = [$filterUid => $categories];

        $this->subject->setAvailableCategoriesForFilter($filterUid, $categories);

        $this->assertEquals($expect, $this->subject->getAvailableCategories());
    }

    /**
     * @test
     */
    public function setAvailableAttributesForAllWillSetAttributesWithAllKey()
    {
        $attributes = [5, 7, 8];

        $this->subject->setAvailableAttributesForAll($attributes);

        $expect = [FiltersAvailableOptions::ALL_FILTERS_KEY => $attributes];
        $this->assertEquals($expect, $this->subject->getAvailableAttributes());
    }

    /**
     * @test
     */
    public function setAvailableAttributesForFilterWillSetAvailableAttributesForFilter()
    {
        $filterUid = 102;
        $attributes = [76, 33, 44];

        $expect = [$filterUid => $attributes];

        $this->subject->setAvailableAttributesForFilter($filterUid, $attributes);

        $this->assertEquals($expect, $this->subject->getAvailableAttributes());
    }
}
