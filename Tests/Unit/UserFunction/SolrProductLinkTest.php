<?php

namespace Pixelant\PxaProductManager\Tests\Unit\UserFunction;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use Pixelant\PxaProductManager\UserFunction\SolrProductLink;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SolrProductLinkTest
 * @package Pixelant\PxaProductManager\Tests\Unit\UserFunction
 */
class SolrProductLinkTest extends UnitTestCase
{
    /**
     * @var SolrProductLink|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockedSolrProductLinkTest = null;

    public function setUp()
    {
        $this->mockedSolrProductLinkTest = $this->createPartialMock(SolrProductLink::class, ['getLinkBuilder']);
        $this->mockedSolrProductLinkTest->cObj = $this->createMock(ContentObjectRenderer::class);
    }

    /**
     * @test
     */
    public function notValidParamsThrowsExpection()
    {
        $params = [];

        $this->expectException(\UnexpectedValueException::class);
        $this->mockedSolrProductLinkTest->getLink('', $params);
    }

    /**
     * @test
     */
    public function getLinkWithValidParamsWillBuildLink()
    {
        $pagePid = 111;
        $productUid = 1;
        $lang = 1;

        $data = [
            'uid' => $productUid,
            '__solr_index_language' => $lang
        ];
        $params = [
            'pageUid' => $pagePid
        ];

        $this->mockedSolrProductLinkTest->cObj->data = $data;

        $mockedLinkBuilder = $this->createMock(LinkBuilderService::class);
        $mockedLinkBuilder
            ->expects($this->once())
            ->method('buildForProduct')
            ->with($pagePid, $productUid)
            ->willReturn('');

        $this->mockedSolrProductLinkTest
            ->expects($this->once())
            ->method('getLinkBuilder')
            ->with($lang)
            ->willReturn($mockedLinkBuilder);

        $this->mockedSolrProductLinkTest->getLink('', $params);
    }
}
