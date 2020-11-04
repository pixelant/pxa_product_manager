<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Controller;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\ProductController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class ProductControllerTest extends UnitTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ProductController
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(ProductController::class)->disableOriginalConstructor()->setMethods(null)->getMock();
    }

    /**
     * @test
     */
    public function readFromSettingsReturnValueOfSettings(): void
    {
        $this->inject(
            $this->subject,
            'settings',
            [
                'categoriesOrderings' => ['orderBy' => 'title', 'orderDirection' => 'asc'],
            ]
        );

        self::assertEquals('title', $this->callInaccessibleMethod($this->subject, 'readFromSettings', 'categoriesOrderings.orderBy'));
    }

    /**
     * @test
     */
    public function readFromSettingsReturnDefaultValueOfSettingsIfNotFound(): void
    {
        $this->inject(
            $this->subject,
            'settings',
            [
                'categoriesOrderings' => ['orderBy' => 'title', 'orderDirection' => 'asc'],
            ]
        );

        self::assertEquals('defaultValue', $this->callInaccessibleMethod($this->subject, 'readFromSettings', 'categoriesOrderings.orderBy.testKey', 'defaultValue'));
    }

    /**
     * @test
     */
    public function createDemandFromSettingsCreateDemandFromSettings(): void
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

        self::assertInstanceOf(ProductDemand::class, $demand);

        self::assertEquals(10, $demand->getLimit());
        self::assertEquals(100, $demand->getOffSet());
        self::assertEquals('test', $demand->getOrderBy());
        self::assertEquals('direction', $demand->getOrderDirection());
        self::assertEquals('allowedOrderBy', $demand->getOrderByAllowed());
        self::assertEquals([1, 2, 3], $demand->getCategories());
        self::assertEquals('or', $demand->getCategoryConjunction());
    }

    /**
     * @test
     */
    public function buildFromSettingsUseSettingsToBuildDemand(): void
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

        self::assertInstanceOf(CategoryDemand::class, $demand);

        self::assertEquals(200, $demand->getLimit());
        self::assertEquals(500, $demand->getOffSet());
        self::assertEquals('title', $demand->getOrderBy());
        self::assertEquals('asc', $demand->getOrderDirection());
        self::assertEquals('allowedOrderBy', $demand->getOrderByAllowed());
        self::assertTrue($demand->isOnlyVisibleInNavigation());
        self::assertTrue($demand->isHideCategoriesWithoutProducts());
        self::assertSame($parent, $demand->getParent());
    }
}
