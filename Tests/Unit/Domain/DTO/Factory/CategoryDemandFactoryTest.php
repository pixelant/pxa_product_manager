<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\DTO;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Domain\Model\DTO\Factory\CategoryDemandFactory;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\DTO
 */
class CategoryDemandFactoryTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new CategoryDemandFactory();
    }

    /**
     * @test
     */
    public function classNameReturnClassNameFromSettings()
    {
        $settings = [
            'demand' => [
                'objects' => ['categoryDemand' => 'test']
            ]
        ];

        $this->assertEquals('test', $this->callInaccessibleMethod($this->subject, 'className', $settings));
    }

    /**
     * @test
     */
    public function classNameReturnOwnClassName()
    {
        $settings = [
        ];

        $this->assertEquals(CategoryDemand::class, $this->callInaccessibleMethod($this->subject, 'className', $settings));
    }

    /**
     * @test
     */
    public function buildFromSettingsUseSettingsToBuildDemand()
    {

        $parent = createEntity(Category::class, 1);
        $settings = [
            'limit' => '200',
            'parent' => $parent,
            'offSet' => '500',
            'categoriesOrderings' => ['orderBy' => 'title', 'orderDirection' => 'asc'],
            'demand' => ['orderByAllowed' => 'allowedOrderBy'],
            'navigation' => [
                'hideCategoriesWithoutProducts' => true,
                'onlyVisibleInNavigation' => true,
            ],
        ];

        $builder = new CategoryDemandFactory();
        $builder->injectDispatcher($this->createMock(Dispatcher::class));

        /** @var CategoryDemand $demand */
        $demand = $builder->buildFromSettings($settings);

        $this->assertInstanceOf(CategoryDemand::class, $demand);

        $this->assertEquals(200, $demand->getLimit());
        $this->assertEquals(500, $demand->getOffSet());
        $this->assertEquals('title', $demand->getOrderBy());
        $this->assertEquals('asc', $demand->getOrderDirection());
        $this->assertEquals('allowedOrderBy', $demand->getOrderByAllowed());
        $this->assertTrue($demand->isOnlyVisibleInNavigation());
        $this->assertTrue($demand->isHideCategoriesWithoutProducts());
        $this->assertSame($parent, $demand->getParent());
    }
}
