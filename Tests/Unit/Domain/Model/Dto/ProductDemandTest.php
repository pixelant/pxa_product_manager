<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model\Dto;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Model\Filter;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class ProductDemandTest extends UnitTestCase
{
    /**
     * @test
     *
     * @dataProvider filtersData
     */
    public function hasFiltersCategoryFilterReturnTrueIfOneOfTheFiltersIsCategoryType($filtes, $expect, $testName)
    {
        $demand = new ProductDemand();
        $demand->setFilters($filtes);

        $this->assertEquals($expect, $demand->hasFiltersCategoryFilter(), $testName);
    }

    /**
     * @test
     */
    public function removeFilterWillRemoveFilter()
    {
        $demand = new ProductDemand();

        $demand->setFilters(['1' => 'test', 2 => 'second']);
        $demand->removeFilter(1);

        $this->assertEquals([2 => 'second'], $demand->getFilters());
    }

    /**
     * @test
     */
    public function removeFilterDoesNothingIfFilterNoSet()
    {
        $demand = new ProductDemand();

        $demand->setFilters(['1' => 'test', 2 => 'second']);
        $demand->removeFilter(3);

        $this->assertEquals(['1' => 'test', 2 => 'second'], $demand->getFilters());
    }

    public function filtersData()
    {
        return [
            'has_only_attributes' => [
                [
                    10 => [
                        'type' => Filter::TYPE_ATTRIBUTES,
                        'value' => [10, 20]
                    ],
                    30 => [
                        'type' => Filter::TYPE_ATTRIBUTES,
                        'value' => [10, 30]
                    ],
                ],
                false,
                'has_only_attributes'
            ],
            'has_category_but_empty' => [
                [
                    10 => [
                        'type' => Filter::TYPE_ATTRIBUTES,
                        'value' => [10, 20]
                    ],
                    30 => [
                        'type' => Filter::TYPE_CATEGORIES,
                        'value' => []
                    ],
                ],
                false,
                'has_category_but_empty'
            ],
            'has_category' => [
                [
                    10 => [
                        'type' => Filter::TYPE_ATTRIBUTES,
                        'value' => [10, 20]
                    ],
                    30 => [
                        'type' => Filter::TYPE_CATEGORIES,
                        'value' => [21, 22]
                    ],
                ],
                true,
                'has_category'
            ],
        ];
    }
}
