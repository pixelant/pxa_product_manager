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
    public function parseFluidStringReturnParsedString()
    {
        $var1 = 'Test test';
        $var2 = ['test' => 'Testing value 2'];

        $string = 'Here goes "{var1}", here goes "{var2.test}"';
        $expect = 'Here goes "' . $var1 . '", here goes "' . $var2['test'] . '"';

        $this->assertEquals($expect, MainUtility::parseFluidString($string, ['var1' => $var1, 'var2' => $var2]));
    }
}
