<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Seo\XmlSitemap;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use Pixelant\PxaProductManager\Seo\XmlSitemap\ProductsXmlSitemapDataProvider;
use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class ProductsXmlSitemapDataProviderTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Seo\XmlSitemap
 */
class ProductsXmlSitemapDataProviderTest extends UnitTestCase
{
    /**
     * @var AccessibleMockObjectInterface|MockObject|ProductsXmlSitemapDataProvider
     */
    protected $subject= null;

    protected function setUp()
    {
        $this->subject = $this->getAccessibleMock(
            ProductsXmlSitemapDataProvider::class,
            ['getLinkBuilderService', 'getLanguageId'],
            [],
            '',
            false
        );

        $GLOBALS['TSFE'] = new \stdClass();
        $GLOBALS['TSFE']->id = 100;
    }

    protected function tearDown()
    {
        unset($GLOBALS['TSFE']);
        unset($this->subject);
    }

    /**
     * @test
     */
    public function defaultExcludeCategoriesIsFalse()
    {
        $this->assertFalse($this->subject->_get('excludeCategories'));
    }

    /**
     * @test
     */
    public function excludeCategoriesIsSetFromConfigArray()
    {
        $config = [
            'url' => [
                'excludeCategories' => 1
            ]
        ];

        $mockedServerRequest = $this->createMock(ServerRequest::class);
        $mockedCobj = $this->createMock(ContentObjectRenderer::class);


        $subject = $this
            ->getMockBuilder(ProductsXmlSitemapDataProvider::class)
            ->setConstructorArgs([$mockedServerRequest, 'test', $config, $mockedCobj])
            ->setMethods(['generateItems'])
            ->getMock();

        $propertyReflection = new \ReflectionProperty($subject, 'excludeCategories');
        $propertyReflection->setAccessible(true);

        $this->assertTrue($propertyReflection->getValue($subject));
    }

    /**
     * @test
     */
    public function pageUidIsSetFromConfigArray()
    {
        $pageUid = 9999;

        $config = [
            'url' => [
                'pageId' => $pageUid
            ]
        ];

        $mockedServerRequest = $this->createMock(ServerRequest::class);
        $mockedCobj = $this->createMock(ContentObjectRenderer::class);

        $subject = $this
            ->getMockBuilder(ProductsXmlSitemapDataProvider::class)
            ->setConstructorArgs([$mockedServerRequest, 'test', $config, $mockedCobj])
            ->setMethods(['generateItems'])
            ->getMock();

        $propertyReflection = new \ReflectionProperty($subject, 'pageId');
        $propertyReflection->setAccessible(true);

        $this->assertEquals($pageUid, $propertyReflection->getValue($subject));
    }

    /**
     * @test
     */
    public function pageUidIsSetFromTSFEIfNoConfig()
    {
        $config = [];

        $mockedServerRequest = $this->createMock(ServerRequest::class);
        $mockedCobj = $this->createMock(ContentObjectRenderer::class);

        $subject = $this
            ->getMockBuilder(ProductsXmlSitemapDataProvider::class)
            ->setConstructorArgs([$mockedServerRequest, 'test', $config, $mockedCobj])
            ->setMethods(['generateItems'])
            ->getMock();

        $propertyReflection = new \ReflectionProperty($subject, 'pageId');
        $propertyReflection->setAccessible(true);

        $this->assertEquals($GLOBALS['TSFE']->id, $propertyReflection->getValue($subject));
    }

    /**
     * @test
     */
    public function defineUrlWillSetLocForData()
    {
        $uid = 123;
        $url = '/test/';

        $data = [
            'data' => [
                'uid' => $uid
            ]
        ];

        $linkBuilderService = $this->createMock(LinkBuilderService::class);
        $linkBuilderService
            ->expects($this->once())
            ->method('buildForProduct')
            ->willReturn($url);

        $this->subject->_set('pageId', 1);

        $this->subject
            ->expects($this->once())
            ->method('getLinkBuilderService')
            ->willReturn($linkBuilderService);

        $this->assertEquals($url, $this->subject->_call('defineUrl', $data)['loc']);
    }

    /**
     * @test
     */
    public function addAdditionalWhereConstraintAddWhere()
    {
        $where = 'test=1';
        $config = ['additionalWhere' => $where];

        $this->subject->_set('config', $config);
        $constraints = [];

        $this->subject->_callRef('addAdditionalWhereConstraint', $constraints);

        $this->assertCount(1, $constraints);
        $this->assertEquals($where, $constraints[0]);
    }

    /**
     * @test
     */
    public function getConfigFieldsReturnDefaultIfNoConfig()
    {
        $config = [];
        $this->subject->_set('config', $config);

        list($pids, $lastModifiedField, $sortField) = $this->subject->_call('getConfigFields');

        $this->assertEquals([], $pids);
        $this->assertEquals('tstamp', $lastModifiedField);
        $this->assertEquals('sorting', $sortField);
    }

    /**
     * @test
     */
    public function getConfigFieldsReturnFieldsFromConfig()
    {
        $config = [
            'pid' => '12,333',
            'lastModifiedField' => 'lastModifiedFieldTest',
            'sortField' => 'sortFieldTest',
        ];

        $this->subject->_set('config', $config);

        list($pids, $lastModifiedField, $sortField) = $this->subject->_call('getConfigFields');

        $this->assertEquals([12, 333], $pids);
        $this->assertEquals('lastModifiedFieldTest', $lastModifiedField);
        $this->assertEquals('sortFieldTest', $sortField);
    }
}
