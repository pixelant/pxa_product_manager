<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Functional\Service\Link;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;

/**
 * Class LinkBuilderServiceTest
 * @package Pixelant\PxaProductManager\Tests\Functional\Service\Link
 */
class LinkBuilderServiceTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    /**
     * @var MockObject|LinkBuilderService
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../../Fixtures/sys_category.xml');
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_pxaproductmanager_domain_model_product.xml');

        $this->subject = $this->getAccessibleMock(LinkBuilderService::class, null);
    }

    /**
     * @test
     */
    public function getProductCategoryUidFindFirstCategoryForProductUid()
    {
        $productUid = 100;
        $expect = 102;

        $this->assertEquals($expect, $this->subject->_call('getProductCategoryUid', $productUid, null));
    }

    /**
     * @test
     */
    public function getCategoriesArgumentsWillBuildTreeOfParentCategories()
    {
        $argPrefix = LinkBuilderService::CATEGORY_ARGUMENT_START_WITH;
        $categoryUid = 6;

        $expect = [
            $argPrefix . '0' => 2,
            $argPrefix . '1' => 3,
            $argPrefix . '2' => 4,
            $argPrefix . '3' => 6,
        ];

        $this->assertEquals($expect, $this->subject->_call('getCategoriesArguments', $categoryUid));
    }
}
