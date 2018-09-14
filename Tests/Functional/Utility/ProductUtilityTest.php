<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Utility;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Utility\ProductUtility;

/**
 * Class ProductUtilityTest
 * @package Pixelant\PxaProductManager\Tests\Functional\Utility
 */
class ProductUtilityTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/tx_pxaproductmanager_domain_model_product.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_category.xml');
    }

    /**
     * @test
     */
    public function numberFormatWillFormatPrice()
    {
        $price = 12345.56;

        $this->assertEquals(
            '12 345.56',
            ProductUtility::formatPrice($price)
        );
    }

    /**
     * @test
     */
    public function getProductCategoriesParentsTreeGenerateTree()
    {
        $categories = ProductUtility::getProductCategoriesParentsTree(1); // product uid = 1
        // This product has two categories uids 3, 4
        // Categories tree are
        // - 1 Root category
        // -- 2 Category 1
        // --- 3 Active category
        // ---- 4 Sub category of active category

        $expectListAndOrder = '3,4,2,1';

        $resultList = [];
        foreach ($categories as $category) {
            $resultList[] = $category->getUid();
        }

        $this->assertEquals(
            $expectListAndOrder,
            implode(',', $resultList)
        );
    }

    /**
     * @test
     */
    public function getProductReversedCategoriesParentsTreeGenerateReversedTree()
    {
        $categories = ProductUtility::getProductCategoriesParentsTree(1, true); // product uid = 1 with reserved order
        // This product has two categories uids 3, 4
        // Categories tree are
        // - 1 Root category
        // -- 2 Category 1
        // --- 3 Active category
        // ---- 4 Sub category of active category

        $expectListAndOrder = '1,2,4,3'; // Reversed order

        $resultList = [];
        foreach ($categories as $category) {
            $resultList[] = $category->getUid();
        }

        $this->assertEquals(
            $expectListAndOrder,
            implode(',', $resultList)
        );
    }
}
