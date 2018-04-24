<?php

namespace Pixelant\PxaProductManager\Tests\Functional\LinkHandler;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\LinkHandler\ProductLinkBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ProductLinkBuilderTest
 * @package Pixelant\PxaProductManager\Tests\Unit\LinkHandler
 */
class ProductLinkBuilderTest extends FunctionalTestCase
{
    /**
     * @var ProductLinkBuilder
     */
    protected $productLinkBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContentObjectRenderer
     */
    protected $mockedContentObjectRenderer;

    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_category.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/tx_pxaproductmanager_domain_model_product.xml');

        $this->mockedContentObjectRenderer = $this->createPartialMock(ContentObjectRenderer::class, ['typolink_URL']);

        $this->productLinkBuilder = GeneralUtility::makeInstance(
            ProductLinkBuilder::class,
            $this->mockedContentObjectRenderer
        );

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

        $this->mockedContentObjectRenderer
            ->expects($this->once())
            ->method('typolink_URL')
            ->willReturn('');

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

        $this->mockedContentObjectRenderer
            ->expects($this->once())
            ->method('typolink_URL')
            ->willReturn('');

        $this->productLinkBuilder->build($linkDetails, $linkText, $target, []);
    }

    protected function tearDown()
    {
        unset($GLOBALS['TSFE']);
    }
}
