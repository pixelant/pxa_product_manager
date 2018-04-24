<?php
namespace Pixelant\PxaProductManager\Tests\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\NavigationController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Utility\MainUtility;

/**
 * Class HelperFunctionsTest
 * @package Pixelant\PxaProductManager\Tests\Utility
 */
class MainUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function getExtMgrConfigurationReturnEmptyConfigrationIfExtConfNotSet()
    {
        $this->assertEquals(
            [],
            MainUtility::getExtMgrConfiguration()
        );
    }

    /**
     * @test
     */
    public function buildLinkArgumentsOnlyProductWithCategories()
    {
        list($category1, $category2, $category3) = $this->getCategoriesForTest();

        $product = new Product();
        $product->_setProperty('uid', 111);

        $product->addCategory($category1);
        $product->addCategory($category2);
        $product->addCategory($category3);

        $expected = [
            'tx_pxaproductmanager_pi1' => [
                NavigationController::CATEGORY_ARG_START_WITH . '0' => $category1->getUid(),
                'product' => $product->getUid()
            ]
        ];

        self::assertEquals(
            $expected,
            MainUtility::buildLinksArguments($product)
        );
    }

    /**
     * @test
     */
    public function buildLinkArgumentsOnlyProductNoCategories()
    {
        $product = new Product();
        $product->_setProperty('uid', 111);

        $expected = [
            'tx_pxaproductmanager_pi1' => [
                'product' => $product->getUid()
            ]
        ];

        self::assertEquals(
            $expected,
            MainUtility::buildLinksArguments($product)
        );
    }

    /**
     * @test
     */
    public function buildLinkArgumentsProductAndCategoryAreSet()
    {
        $product = new Product();
        $product->_setProperty('uid', 111);

        $activeCategory = new Category();
        $activeCategory->_setProperty('uid', 222);

        list($category1, $category2, $category3) = $this->getCategoriesForTest();
        // simulate tree
        $activeCategory->setParent($category1);
        $category1->setParent($category2);
        $category2->setParent($category3);

        $expected = [
            'tx_pxaproductmanager_pi1' => [
                NavigationController::CATEGORY_ARG_START_WITH . '0' => $category2->getUid(),
                NavigationController::CATEGORY_ARG_START_WITH . '1' => $category1->getUid(),
                NavigationController::CATEGORY_ARG_START_WITH . '2' => $activeCategory->getUid(),
                'product' => $product->getUid()
            ]
        ];

        self::assertEquals(
            $expected,
            MainUtility::buildLinksArguments($product, $activeCategory)
        );
    }

    /**
     * @test
     */
    public function buildLinkArgumentsOnlyCategoryIsSet()
    {
        $activeCategory = new Category();
        $activeCategory->_setProperty('uid', 222);

        list($category1, $category2, $category3) = $this->getCategoriesForTest();
        // simulate tree
        $activeCategory->setParent($category1);
        $category1->setParent($category2);
        $category2->setParent($category3);

        $expected = [
            'tx_pxaproductmanager_pi1' => [
                NavigationController::CATEGORY_ARG_START_WITH . '0' => $category2->getUid(),
                NavigationController::CATEGORY_ARG_START_WITH . '1' => $category1->getUid(),
                NavigationController::CATEGORY_ARG_START_WITH . '2' => $activeCategory->getUid()
            ]
        ];

        self::assertEquals(
            $expected,
            MainUtility::buildLinksArguments(null, $activeCategory)
        );
    }

    /**
     * Custom categories set
     *
     * @return array
     */
    protected function getCategoriesForTest()
    {
        $category1 = new Category();
        $category1->_setProperty('uid', 123);
        $category1->setTitle('Test123');

        $category2 = new Category();
        $category2->_setProperty('uid', 321);
        $category2->setTitle('Test321');

        $category3 = new Category();
        $category3->_setProperty('uid', 456);
        $category3->setTitle('Test456');

        return [$category1, $category2, $category3];
    }
}
