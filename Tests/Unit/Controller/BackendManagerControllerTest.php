<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Controller;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\BackendManagerController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;

/**
 * Class BackendManagerControllerTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Controller
 */
class BackendManagerControllerTest extends UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface
     */
    protected $mockedBackendManagerController;

    public function setUp()
    {
        $this->mockedBackendManagerController = $this->getAccessibleMock(
            BackendManagerController::class,
            ['dummy'],
            [],
            '',
            false
        );
    }

    /**
     * @test
     */
    public function getCategoriesWithProductsReturnCategoryToProducts()
    {
        list($category1, $category2) = $this->getCategories();

        $mockedProductRepository = $this->createPartialMock(ProductRepository::class, ['findProductsByCategories']);
        $mockedProductRepository
            ->expects($this->exactly(2))
            ->method('findProductsByCategories');
        $this->mockedBackendManagerController->_set('productRepository', $mockedProductRepository);

        $expect = [12, 21];
        $result = array_keys(
            $this->mockedBackendManagerController->_call(
                'getCategoriesWithProducts',
                [$category1, $category2]
            )
        );

        $this->assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function getCategoriesWithProductsWithEmptyCategoriesGetEmptyArray()
    {
        $this->assertCount(
            0,
            $this->mockedBackendManagerController->_call('getCategoriesWithProducts', [])
        );
    }

    /**
     * @test
     */
    public function buildBreadCrumbsFromCategory()
    {
        list($category1, $category2, $category3) = $this->getCategories();

        $result = [$category1, $category2, $category3];

        $this->assertEquals(
            $result,
            $this->mockedBackendManagerController->_call('buildCategoryBreadCrumbs', $category3)
        );
    }

    /**
     * @test
     */
    public function generateCorrectPositioningArrayFromRecordsArray()
    {
        list($category1, $category2, $category3) = $this->getCategories();
        $pid = 1000;
        $this->mockedBackendManagerController->_set('pid', $pid);

        $expect = [
            'prev' => [
                21 => $pid,
                333 => -12
            ],
            'next' => [
                12 => -21,
                21 => -333
            ]
        ];

        $this->assertEquals(
            $expect,
            $this->mockedBackendManagerController->_call('generatePositionsArray', [$category1, $category2, $category3])
        );
    }

    /**
     * Create categories for test
     *
     * @return array
     */
    protected function getCategories()
    {
        $category1 = new Category();
        $category1->_setProperty('uid', 12);

        $category2 = new Category();
        $category2->_setProperty('uid', 21);
        $category2->_setProperty('parent', $category1);

        $category3 = new Category();
        $category3->_setProperty('uid', 333);
        $category3->_setProperty('parent', $category2);

        return [$category1, $category2, $category3];
    }
}
