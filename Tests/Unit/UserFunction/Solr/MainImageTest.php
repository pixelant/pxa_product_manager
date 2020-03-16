<?php

namespace Pixelant\PxaProductManager\Tests\Unit\UserFunction\Solr;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\UserFunction\Solr\MainImage;
use TYPO3\CMS\Core\Resource\FileReference;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\UserFunction
 */
class MainImageTest extends UnitTestCase
{
    /**
     * @var MainImage
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(MainImage::class)->setMethods(['getImages', 'findMatchedImage'])->getMock();
    }

    /**
     * @test
     */
    public function getUrlWithEmptyImagesReturnEmptyUrl()
    {
        $this->subject->expects($this->once())->method('getImages')->willReturn([]);

        $this->assertEquals('', $this->subject->getUrl());
    }

    /**
     * @test
     */
    public function getUrlReturnFirstMatchedImagePublicUrl()
    {
        $file1 = $this->createMock(FileReference::class);
        $file2 = $this->createMock(FileReference::class);
        $file3 = $this->createMock(FileReference::class);
        $file4 = $this->createMock(FileReference::class);

        $this->subject->expects($this->once())->method('getImages')->willReturn([
            $file1, $file2, $file3, $file4
        ]);
        $this->subject->expects($this->once())->method('findMatchedImage')->willReturn($file3);

        $file3->expects($this->once())->method('getPublicUrl')->willReturn('url');
        $this->assertEquals('url', $this->subject->getUrl());
    }

    /**
     * @test
     */
    public function getUrlReturnFirstNonMatchedImageIfNotFound()
    {
        $file1 = $this->createMock(FileReference::class);
        $file2 = $this->createMock(FileReference::class);
        $file3 = $this->createMock(FileReference::class);
        $file4 = $this->createMock(FileReference::class);

        $this->subject->expects($this->once())->method('getImages')->willReturn([
            $file1, $file2, $file3, $file4
        ]);
        $this->subject->expects($this->once())->method('findMatchedImage')->willReturn(null);

        $file1->expects($this->once())->method('getPublicUrl')->willReturn('file1');
        $this->assertEquals('file1', $this->subject->getUrl());
    }
}
