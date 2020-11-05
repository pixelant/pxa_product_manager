<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\UserFunction;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use Pixelant\PxaProductManager\UserFunction\Breadcrumbs;
use TYPO3\CMS\Core\Http\ServerRequest;

class BreadcrumbsTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this
            ->getMockBuilder(Breadcrumbs::class)
            ->disableOriginalConstructor()
            ->setMethods(['url'])
            ->getMock();
    }

    /**
     * @test
     */
    public function hasBreadcrumbsReturnTruIfGetParametersExist(): void
    {
        $request = $this->prophesize(ServerRequest::class);
        $request->getQueryParams()->shouldBeCalled()->willReturn(['tx_pxaproductmanager_pi1' => '12']);

        $this->inject($this->subject, 'request', $request->reveal());

        self::assertTrue($this->callInaccessibleMethod($this->subject, 'hasBreadcrumbs'));
    }

    /**
     * @test
     */
    public function hasBreadcrumbsReturnFalseIfNoParams(): void
    {
        $request = $this->prophesize(ServerRequest::class);
        $request->getQueryParams()->shouldBeCalled()->willReturn([]);

        $this->inject($this->subject, 'request', $request->reveal());

        self::assertFalse($this->callInaccessibleMethod($this->subject, 'hasBreadcrumbs'));
    }

    /**
     * @test
     */
    public function getArgumentsReturnArguments(): void
    {
        $arguments = [
            'tx_pxaproductmanager_pi1' => [
                'action' => 'list',
                'category' => '13',
                'category_0' => '9',
                'controller' => 'Product',
                'product' => '1',
            ],
        ];

        $request = $this->prophesize(ServerRequest::class);
        $request->getQueryParams()->shouldBeCalled()->willReturn($arguments);

        $this->inject($this->subject, 'request', $request->reveal());

        self::assertEquals(
            $arguments['tx_pxaproductmanager_pi1'],
            $this->callInaccessibleMethod($this->subject, 'getArguments')
        );
    }

    /**
     * @test
     */
    public function addProductToBreadCrumbsCreateItemWithUrlOfGivenArguments(): void
    {
        $arguments = [
            'tx_pxaproductmanager_pi1' => [
                'action' => 'list',
                'category' => '13',
                'category_0' => '9',
                'controller' => 'Product',
                'product' => '1',
            ],
        ];
        $request = $this->prophesize(ServerRequest::class);
        $request->getQueryParams()->shouldBeCalled()->willReturn($arguments);
        $this->inject($this->subject, 'request', $request->reveal());

        $repo = $this->prophesize(ProductRepository::class);
        $repo->findByUid(1)->shouldBeCalled()->willReturn(
            TestsUtility::createEntity(Product::class, ['name' => 'test'])
        );

        $this->inject($this->subject, 'productRepository', $repo->reveal());

        $expect = $arguments['tx_pxaproductmanager_pi1'];
        $expect['action'] = 'show';

        $this->subject->expects(self::once())->method('url')->with($expect);
        $this->callInaccessibleMethod($this->subject, 'addProduct');
    }

    /**
     * @test
     */
    public function filterCategoriesArgumentsReturnArgumentsThatStartWithCategory(): void
    {
        $arguments = [
            'tx_pxaproductmanager_pi1' => [
                'action' => 'list',
                'category' => '13',
                'category_0' => '9',
                'controller' => 'Product',
                'product' => '1',
            ],
        ];

        $expect = [
            'category' => '13',
            'category_0' => '9',
        ];

        self::assertEquals(
            $expect,
            $this->callInaccessibleMethod(
                $this->subject,
                'filterCategoriesArguments',
                $arguments['tx_pxaproductmanager_pi1']
            )
        );
    }

    /**
     * @test
     */
    public function renameCategoriesArgumentsWillCorrectArgumentsNameForCategories(): void
    {
        $arguments = [
            'category_2' => '6',
            'category_3' => '7',
            'category_4' => '9',
            'category_5' => '10',
        ];

        $expect = [
            'category' => '6',
            'category_0' => '7',
            'category_1' => '9',
            'category_2' => '10',
        ];

        self::assertEquals(
            $expect,
            $this->callInaccessibleMethod(
                $this->subject,
                'renameCategoriesArguments',
                $arguments
            )
        );
    }

    /**
     * @test
     */
    public function addCategoriesNeverCallRepositoryFindMethodIfEmptyArguments(): void
    {
        $subject = $this
            ->getMockBuilder(Breadcrumbs::class)
            ->setMethods(['filterCategoriesArguments', 'getArguments'])
            ->disableOriginalConstructor()
            ->getMock();

        $subject->expects(self::once())->method('filterCategoriesArguments')->willReturn([]);

        $mockedRepository = $this->createMock(CategoryRepository::class);
        $mockedRepository->expects(self::never())->method('findByUids');

        $this->inject($subject, 'categoryRepository', $mockedRepository);

        $this->callInaccessibleMethod($subject, 'addCategories');
    }
}
