<?php

namespace Pixelant\PxaProductManager\Tests\Unit\UserFunction;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\UserFunction\SolrIndexImage;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SolrIndexImageTest
 * @package Pixelant\PxaProductManager\Tests\Unit\UserFunction
 */
class SolrIndexImageTest extends UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface|SolrIndexImage
     */
    protected $solrIndexImage;

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->solrIndexImage = $this->getAccessibleMock(
            SolrIndexImage::class,
            ['getProductImages'],
            [],
            '',
            false
        );

        $this->solrIndexImage->cObj = $this->createMock(ContentObjectRenderer::class);
    }

    /**
     * @test
     */
    public function getProductImageWillReturnMainImage()
    {
        $field = 'pxapm_main_image';

        /** @var \PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface $mainImage */
        $image = $this->getMockBuilder(FileReference::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperty'])
            ->getMock();

        $image->expects($this->once())
            ->method('getProperty')
            ->with($field)
            ->willReturn(1);

        $this->solrIndexImage
            ->expects($this->once())
            ->method('getProductImages')
            ->willReturn([$image]);

        $result = $this->solrIndexImage->_call('getProductImage', $field);

        $this->assertSame(
            $image,
            $result
        );
    }

    protected function tearDown()
    {
        unset($this->solrIndexImage);
    }
}
