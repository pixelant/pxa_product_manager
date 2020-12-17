<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Controller;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\ProductShowController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;

class ProductShowControllerTest extends UnitTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ProductShowController
     */
    protected $subject;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ListenerProvider
     */
    protected $listenerProviderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|EventDispatcher
     */
    protected $eventDispatcherMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this
            ->getMockBuilder(ProductShowController::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->listenerProviderMock = $this
            ->getMockBuilder(ListenerProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['getListenersForEvent'])
            ->getMock();

        $this->eventDispatcherMock = $this
            ->getMockBuilder(EventDispatcher::class)
            ->setConstructorArgs([$this->listenerProviderMock])
            ->setMethodsExcept(['dispatch'])
            ->getMock();
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

        self::assertEquals(
            'title',
            $this->callInaccessibleMethod(
                $this->subject,
                'readFromSettings',
                'categoriesOrderings.orderBy'
            )
        );
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

        self::assertEquals(
            'defaultValue',
            $this->callInaccessibleMethod(
                $this->subject,
                'readFromSettings',
                'categoriesOrderings.orderBy.testKey',
                'defaultValue'
            )
        );
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
            'pageTreeStartingPoint' => 1,
        ];

        $this->subject->injectDispatcher($this->eventDispatcherMock);

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
        self::assertEquals(1, $demand->getPageTreeStartingPoint());
    }

    /**
     * @test
     */
    public function buildFromSettingsUseSettingsToBuildDemand(): void
    {
        $parent = TestsUtility::createEntity(Category::class, 1);
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

        $this->subject->injectDispatcher($this->eventDispatcherMock);
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
