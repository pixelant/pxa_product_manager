<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\DTO;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\DTO\Factory\ProductDemandFactory;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\DTO
 */
class ProductDemandFactoryTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new ProductDemandFactory();
    }

    /**
     * @test
     */
    public function classNameReturnClassNameFromSettings()
    {
        $settings = [
            'demand' => [
                'objects' => ['productDemand' => 'productDemandObject']
            ]
        ];

        $this->assertEquals('productDemandObject', $this->callInaccessibleMethod($this->subject, 'className', $settings));
    }

    /**
     * @test
     */
    public function classNameReturnOwnClassName()
    {
        $settings = [
        ];

        $this->assertEquals(ProductDemand::class, $this->callInaccessibleMethod($this->subject, 'className', $settings));
    }

    /**
     * @test
     */
    public function buildFromSettingsUseSettingsToBuildDemand()
    {

        $settings = [
            'limit' => '10',
            'offSet' => '100',
            'productOrderings' => ['orderBy' => 'test', 'orderDirection' => 'direction'],
            'demand' => ['orderByAllowed' => 'allowedOrderBy'],
            'categories' => [1, 2, 3],
            'categoryConjunction' => 'or',
        ];

        $builder = new ProductDemandFactory();
        $builder->injectDispatcher($this->createMock(Dispatcher::class));

        /** @var ProductDemand $demand */
        $demand = $builder->buildFromSettings($settings);

        $this->assertInstanceOf(ProductDemand::class, $demand);

        $this->assertEquals(10, $demand->getLimit());
        $this->assertEquals(100, $demand->getOffSet());
        $this->assertEquals('test', $demand->getOrderBy());
        $this->assertEquals('direction', $demand->getOrderDirection());
        $this->assertEquals('allowedOrderBy', $demand->getOrderByAllowed());
        $this->assertEquals([1, 2, 3], $demand->getCategories());
        $this->assertEquals('or', $demand->getCategoryConjunction());
    }
}
