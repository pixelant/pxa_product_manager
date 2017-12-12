<?php
namespace Pixelant\PxaProductManager\Tests\Functional\Utility;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Utility\MainUtility;

/**
 * Class HelperFunctionsTest
 * @package Pixelant\PxaProductManager\Tests\Utility
 */
class MainUtilityTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/tx_pxaproductmanager_domain_model_product.xml');
    }

    /**
     * @test
     */
    public function buildLinkArgumentsOnlyProductIntegerNoCategories()
    {
        $product = 100;

        $expected = [
            'tx_pxaproductmanager_pi1' => [
                'product' => $product
            ]
        ];

        self::assertEquals(
            $expected,
            MainUtility::buildLinksArguments($product)
        );
    }
}
