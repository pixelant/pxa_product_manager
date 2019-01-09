<?php

namespace Pixelant\PxaProductManager\Tests\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Utility\ProductUtility;
use Pixelant\PxaProductManager\Domain\Model\Category;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

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
    public function getWishListReturnEmptyArrayWhenNoProductsUids()
    {
        $_COOKIE[ProductUtility::WISH_LIST_COOKIE_NAME] = '';
        $expected = [];
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

    /**
     * @test
     */
    public function canGetCalculatedCustomSorting()
    {
        $product = new Product();
        $product->_setProperty('uid', 4);
        // When ConfigurationManager is in 'BE' mode it uses the product pid
        // to set what page to get configuration from, set it to 100 here to
        // make it unique since function is static
        $product->_setProperty('pid', 100);

        $configuration = [
            'plugin.' => [
                'tx_pxaproductmanager.' => [
                    'settings.' => [
                        'additionalClasses.' => [
                            'categories.' => [
                                '33' => 'class-33'
                            ],
                            'launched.' => [
                                'isNewClass' => 'class-new'
                            ]
                        ],
                        'launched.' => [
                            'dateIntervalAsNew' => 'P14D'
                        ],
                        'customSorting.' => [
                            'enable' => 1,
                            'points' => [
                                'new' => 22,
                                'categories' => [
                                    '33' => 33,
                                    '44' => 44,
                                    '55' => 55,
                                    '66' => 66
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $category = $this->getMockBuilder(Category::class)
            ->setMethods(['getUid'])
            ->disableOriginalConstructor()
            ->getMock();
        $category->method('getUid')->will(self::returnValue(33));
        $product->addCategory($category);

        $configurationUtilityMock = $this->getMockBuilder(\Pixelant\PxaProductManager\Utility\ConfigurationUtility::class)
            ->setMethods(['getSettings'])
            ->getMock();
        $configurationManagerMock = $this->getMockBuilder(\Pixelant\PxaProductManager\Configuration\ConfigurationManager::class)
            ->setMethods(['getConfiguration'])
            ->getMock();
        $environmentServiceMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Service\EnvironmentService::class)
            ->setMethods(array('isEnvironmentInFrontendMode'))
            ->getMock();

        ObjectAccess::setProperty($configurationUtilityMock, 'configurationManager', $configurationManagerMock, true);
        ObjectAccess::setProperty($configurationManagerMock, 'environmentService', $environmentServiceMock, true);

        $environmentServiceMock->method('isEnvironmentInFrontendMode')->will($this->returnValue(false));
        $configurationManagerMock->method('getConfiguration')->will($this->returnValue($configuration));

        // Check that customSorting is calculated correctly when "isNew"
        $launched = new \DateTime();
        $launched->modify('-13 days');
        $product->setLaunched($launched);

        self::assertEquals(
            55,
            ProductUtility::getCalculatedCustomSorting($product)
        );

        // Check that customSorting is calculated correctly when not "isNew"
        $launched = new \DateTime();
        $launched->modify('-14 days');
        $product->setLaunched($launched);

        self::assertEquals(
            33,
            ProductUtility::getCalculatedCustomSorting($product)
        );

        // Check that customSorting is calculated with multiple categories and not "isNew"
        $category = $this->getMockBuilder(Category::class)
            ->setMethods(['getUid'])
            ->disableOriginalConstructor()
            ->getMock();
        $category->method('getUid')->will(self::returnValue(44));
        $product->addCategory($category);

        self::assertEquals(
            77,
            ProductUtility::getCalculatedCustomSorting($product)
        );

        // Check that customSorting is calculated with multiple categories and "isNew"
        $launched = new \DateTime();
        $launched->modify('-13 days');
        $product->setLaunched($launched);

        $product->addCategory($category);

        self::assertEquals(
            99,
            ProductUtility::getCalculatedCustomSorting($product)
        );
    }

    /**
     * @test
     */
    public function calculateTotalPriceOfOrderProductsWithoutFormatCalculateTotalPrice()
    {
        $price1 = 12.50;
        $price2 = 1.75;
        $quantity1 = 3;
        $quantity2 = 1;

        $order = new Order();

        $product1 = new Product();
        $product1->_setProperty('uid', 1);

        $product2 = new Product();
        $product2->_setProperty('uid', 2);

        $productsQuantity = [
            1 => [
                'quantity' => $quantity1,
                'price' => $price1
            ],
            2 => [
                'quantity' => $quantity2,
                'price' => $price2
            ],
        ];

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product1);
        $objectStorage->attach($product2);

        $order->setProductsQuantity($productsQuantity);
        $order->setProducts($objectStorage);

        $expect = ($price1 * $quantity1) + ($price2 * $quantity2);

        $this->assertEquals($expect, ProductUtility::calculateOrderTotalPrice($order));
    }

    /**
     * @test
     */
    public function calculateTotalTaxOfOrderProductsWithoutFormatCalculateTotalTax()
    {
        $price1 = 121.50;
        $price2 = 14.35;
        $quantity1 = 2;
        $quantity2 = 4;

        $taxRate = 18.00;

        $order = new Order();

        $product1 = new Product();
        $product1->_setProperty('uid', 1);

        $product2 = new Product();
        $product2->_setProperty('uid', 2);

        $productsQuantity = [
            1 => [
                'quantity' => $quantity1,
                'price' => $price1,
                'tax' => $price1 * ($taxRate / 100)
            ],
            2 => [
                'quantity' => $quantity2,
                'price' => $price2,
                'tax' => $price2 * ($taxRate / 100)
            ]
        ];

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product1);
        $objectStorage->attach($product2);

        $order->setProductsQuantity($productsQuantity);
        $order->setProducts($objectStorage);

        $expect = (($price1 * ($taxRate / 100)) * $quantity1) + (($price2 * ($taxRate / 100)) * $quantity2);

        $this->assertEquals($expect, ProductUtility::calculateOrderTotalTax($order));
    }
}
