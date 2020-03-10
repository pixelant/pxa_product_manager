<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Controller;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\ProductController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit
 */
class ProductControllerTest extends UnitTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ProductController
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(ProductController::class)->disableOriginalConstructor()->setMethods(null)->getMock();
    }

    /**
     * @test
     */
    public function readFromSettingsReturnValueOfSettings()
    {
        $this->inject(
            $this->subject,
            'settings',
            [
                'categoriesOrderings' => ['orderBy' => 'title', 'orderDirection' => 'asc'],
            ]
        );

        $this->assertEquals('title', $this->callInaccessibleMethod($this->subject, 'readFromSettings', 'categoriesOrderings.orderBy'));
    }

    /**
     * @test
     */
    public function readFromSettingsReturnDefaultValueOfSettingsIfNotFound()
    {
        $this->inject(
            $this->subject,
            'settings',
            [
                'categoriesOrderings' => ['orderBy' => 'title', 'orderDirection' => 'asc'],
            ]
        );

        $this->assertEquals('defaultValue', $this->callInaccessibleMethod($this->subject, 'readFromSettings', 'categoriesOrderings.orderBy.testKey', 'defaultValue'));
    }

    /**
     * @test
     */
    public function createDemandFromSettingsCreateDemandFromSettings()
    {
        $settings = [
            'limit' => '10',
            'offSet' => '100',
            'demand' => ['orderByAllowed' => 'allowedOrderBy'],
            'categories' => [1, 2, 3],
            'categoryConjunction' => 'or',
        ];

        $this->subject->injectDispatcher($this->createMock(Dispatcher::class));
        $this->inject(
            $this->subject,
            'settings',
            [
                'productOrderings' => ['orderBy' => 'test', 'orderDirection' => 'direction'],
            ]
        );

        $demand = $this->callInaccessibleMethod($this->subject, 'createProductsDemand', $settings);

        $this->assertInstanceOf(ProductDemand::class, $demand);

        $this->assertEquals(10, $demand->getLimit());
        $this->assertEquals(100, $demand->getOffSet());
        $this->assertEquals('test', $demand->getOrderBy());
        $this->assertEquals('direction', $demand->getOrderDirection());
        $this->assertEquals('allowedOrderBy', $demand->getOrderByAllowed());
        $this->assertEquals([1, 2, 3], $demand->getCategories());
        $this->assertEquals('or', $demand->getCategoryConjunction());
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
            'demand' => ['orderByAllowed' => 'allowedOrderBy'],
            'navigation' => [
                'hideCategoriesWithoutProducts' => true,
            ],
            'onlyVisibleInNavigation' => true,
        ];

        $this->subject->injectDispatcher($this->createMock(Dispatcher::class));
        $this->inject(
            $this->subject,
            'settings',
            [
                'categoriesOrderings' => ['orderBy' => 'title', 'orderDirection' => 'asc'],
            ]
        );

        /** @var CategoryDemand $demand */
        $demand = $this->callInaccessibleMethod($this->subject, 'createCategoriesDemand', $settings);

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
