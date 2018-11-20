<?php
namespace Pixelant\PxaProductManager\Tests\Unit\UserFunction;

use Nimut\TestingFramework\TestCase\UnitTestCase;
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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cObj = null;

    public function setUp()
    {
        $this->mockedSolrProductLinkTest = $this->createPartialMock(SolrProductLink::class, ['buildLinksArguments']);
        $this->cObj = $this->createMock(ContentObjectRenderer::class);

        $this->mockedSolrProductLinkTest->cObj = $this->cObj;
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
        $data = [
            'uid' => 1,
            '__solr_index_language' => 1
        ];
        $testParams = ['testparam' => 'test'];
        $params = [
            'pageUid' => $pagePid
        ];

        $this->cObj->data = $data;

        $this->mockedSolrProductLinkTest
            ->expects($this->once())
            ->method('buildLinksArguments')
            ->willReturn($testParams);

        $urlParams = [
            'parameter' => $pagePid,
            'useCacheHash' => 1,
            'additionalParams' => '&L=1&' . http_build_query($testParams),
        ];

        $this->cObj
            ->expects($this->once())
            ->method('typolink_URL')
            ->with($urlParams)
            ->willReturn('');

        $this->mockedSolrProductLinkTest->getLink('', $params);
    }
}
