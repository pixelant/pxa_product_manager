<?php
namespace Pixelant\PxaProductManager\Tests\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Utility\ProductUtility;

/**
 * Class ProductUtilityTest
 * @package Pixelant\PxaProductManager\Tests\Utility
 */
class ProductUtilityTest extends UnitTestCase
{
    protected function setUp()
    {
        // Simulate cookie list
        $_COOKIE[ProductUtility::WISH_LIST_COOKIE_NAME] = implode(',', $this->getProductsUids());
    }

    protected function tearDown()
    {
        unset($_COOKIE[ProductUtility::WISH_LIST_COOKIE_NAME]);
    }

    /**
     * @test
     */
    public function getWishListReturnArrayOfProductsUids()
    {
        $expected = $this->getProductsUids();
        $result = ProductUtility::getWishList();

        $this->assertEquals(
            $expected,
            $result
        );
    }

    /**
     * @test
     */
    public function isProductInWishListReturnTrueIfProductInListAndProductIsObject()
    {
        $product = new Product();
        $product->_setProperty('uid', 3);

        $this->assertTrue(
            ProductUtility::isProductInWishList($product)
        );
    }

    /**
     * @test
     */
    public function isProductInWishListReturnFalseIfProductInNotListAndProductIsObject()
    {
        $product = new Product();
        $product->_setProperty('uid', 4);

        $this->assertFalse(
            ProductUtility::isProductInWishList($product)
        );
    }

    /**
     * @test
     */
    public function isProductInWishListReturnTrueIfProductInListAndProductIsUid()
    {
        $product = 3;

        $this->assertTrue(
            ProductUtility::isProductInWishList($product)
        );
    }

    /**
     * @test
     */
    public function isProductInWishListReturnFalseIfProductInNotListAndProductIsUid()
    {
        $product = 4;

        $this->assertFalse(
            ProductUtility::isProductInWishList($product)
        );
    }

    /**
     * Simulate products uids
     *
     * @return array
     */
    protected function getProductsUids()
    {
        return [1, 2, 3];
    }
}
