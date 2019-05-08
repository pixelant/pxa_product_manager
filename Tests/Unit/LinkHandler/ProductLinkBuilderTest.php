<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\LinkHandler;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Pixelant\PxaProductManager\LinkHandler\ProductLinkBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * Class ProductLinkBuilderTest
 * @package Pixelant\PxaProductManager\Tests\Unit\LinkHandler
 */
class ProductLinkBuilderTest extends UnitTestCase
{
    /**
     * @var ProductLinkBuilder|MockObject
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(
            ProductLinkBuilder::class,
            ['getTypoScriptFrontendController'],
            [],
            '',
            false,
            false
        );
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getLinkBuilderPassGiveTypoScriptFrontendController()
    {
        $this->subject
            ->expects($this->once())
            ->method('getTypoScriptFrontendController');

        $this->subject->_call('getLinkBuilder');
    }

    /**
     * @test
     */
    public function productLinkBuilderModeDefinedIfProductGivenInLinkDetails()
    {
        $linkDetails = [
            'product' => 123
        ];

        $this->subject->_call('defineBuilderMode', $linkDetails);

        $this->assertEquals(ProductLinkBuilder::PRODUCT_MODE, $this->subject->_get('mode'));
        $this->assertEquals(123, $this->subject->_get('recordUid'));
    }

    /**
     * @test
     */
    public function categoryLinkBuilderModeDefinedIfCategoryGivenInLinkDetails()
    {
        $linkDetails = [
            'category' => 321
        ];

        $this->subject->_call('defineBuilderMode', $linkDetails);

        $this->assertEquals(ProductLinkBuilder::CATEGORY_MODE, $this->subject->_get('mode'));
        $this->assertEquals(321, $this->subject->_get('recordUid'));
    }

    /**
     * @test
     */
    public function defineBuilderModeThrowExcpetionIfNeitherProductNorCategoryGiven()
    {
        $linkDetails = [];

        $this->expectException(\InvalidArgumentException::class);
        $this->subject->_call('defineBuilderMode', $linkDetails);
    }

    /**
     * @test
     */
    public function ifTypo3RequestProvideValidSiteItsReturned()
    {
        $site = $this->createMock(Site::class);

        $request = $this->createPartialMock(ServerRequest::class, ['getAttribute']);
        $request
            ->expects($this->once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($site);

        $requestBackup = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $resultSite = $this->subject->_call('getSite');

        $GLOBALS['TYPO3_REQUEST'] = $requestBackup;

        $this->assertSame($site, $resultSite);
    }
}
