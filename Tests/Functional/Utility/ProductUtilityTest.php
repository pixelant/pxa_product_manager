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
}
