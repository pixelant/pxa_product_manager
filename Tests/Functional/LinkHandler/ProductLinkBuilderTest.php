<?php

namespace Pixelant\PxaProductManager\Tests\Functional\LinkHandler;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Pixelant\PxaProductManager\LinkHandler\ProductLinkBuilder;
use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ProductLinkBuilderTest
 * @package Pixelant\PxaProductManager\Tests\Unit\LinkHandler
 */
class ProductLinkBuilderTest extends FunctionalTestCase
{
    /**
     * @var ProductLinkBuilder|MockObject
     */
    protected $productLinkBuilder;

    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_category.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/tx_pxaproductmanager_domain_model_product.xml');

        $this->productLinkBuilder = $this->createPartialMock(ProductLinkBuilder::class, ['getLinkBuilder']);

        $tsfe = $this->createMock(TypoScriptFrontendController::class);

        $tmpl = new \stdClass();
        $tmpl->setup['plugin.']['tx_pxaproductmanager.']['settings.'] = [
            'pagePid' => 123
        ];

        $tsfe->tmpl = $tmpl;

        $GLOBALS['TSFE'] = $tsfe;
    }

    /**
     * @test
     */
    public function buildLinkWithoutCorrectParametersWillGenerateEmptyStringFinalUrl()
    {
        $linkDetails['product'] = 0;
        $linkText = 'Test link';
        $target = '';
        $finalUrl = '';

        $this->assertEquals(
            [$finalUrl, $linkText, $target],
            $this->productLinkBuilder->build($linkDetails, $linkText, $target, [])
        );
    }

    /**
     * @test
     */
    public function buildLinkForProductGenerateLinkForProductAndItCategories()
    {
        $linkDetails['product'] = 1;
        $linkText = 'Test link';
        $target = '';

        $mockedLinkBuilder = $this->createPartialMock(LinkBuilderService::class, ['buildForProduct']);
        $mockedLinkBuilder
            ->expects($this->once())
            ->method('buildForProduct');

        $this->productLinkBuilder
            ->expects($this->once())
            ->method('getLinkBuilder')
            ->willReturn($mockedLinkBuilder);

        $this->productLinkBuilder->build($linkDetails, $linkText, $target, []);
    }

    /**
     * @test
     */
    public function buildLinkForCategoryGenerateLinkForCategoryAndItsParentCategories()
    {
        $linkDetails['category'] = 3;
        $linkText = 'Test link';
        $target = '';

        $mockedLinkBuilder = $this->createPartialMock(LinkBuilderService::class, ['buildForCategory']);
        $mockedLinkBuilder
            ->expects($this->once())
            ->method('buildForCategory');

        $this->productLinkBuilder
            ->expects($this->once())
            ->method('getLinkBuilder')
            ->willReturn($mockedLinkBuilder);

        $this->productLinkBuilder->build($linkDetails, $linkText, $target, []);
    }

    protected function tearDown()
    {
        unset($GLOBALS['TSFE']);
    }
}
