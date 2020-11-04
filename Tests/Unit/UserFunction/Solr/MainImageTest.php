<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\UserFunction\Solr;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\UserFunction\Solr\MainImage;
use TYPO3\CMS\Core\Resource\FileReference;

class MainImageTest extends UnitTestCase
{
    /**
     * @var MainImage
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(MainImage::class)->setMethods(['getImages', 'findMatchedImage'])->getMock();
    }

    /**
     * @test
     */
    public function getUrlWithEmptyImagesReturnEmptyUrl(): void
    {
        $this->subject->expects(self::once())->method('getImages')->willReturn([]);

        self::assertEquals('', $this->subject->getUrl());
    }

    /**
     * @test
     */
    public function getUrlReturnFirstMatchedImagePublicUrl(): void
    {
        $file1 = $this->createMock(FileReference::class);
        $file2 = $this->createMock(FileReference::class);
        $file3 = $this->createMock(FileReference::class);
        $file4 = $this->createMock(FileReference::class);

        $this->subject->expects(self::once())->method('getImages')->willReturn([
            $file1, $file2, $file3, $file4,
        ]);
        $this->subject->expects(self::once())->method('findMatchedImage')->willReturn($file3);

        $file3->expects(self::once())->method('getPublicUrl')->willReturn('url');
        self::assertEquals('url', $this->subject->getUrl());
    }

    /**
     * @test
     */
    public function getUrlReturnFirstNonMatchedImageIfNotFound(): void
    {
        $file1 = $this->createMock(FileReference::class);
        $file2 = $this->createMock(FileReference::class);
        $file3 = $this->createMock(FileReference::class);
        $file4 = $this->createMock(FileReference::class);

        $this->subject->expects(self::once())->method('getImages')->willReturn([
            $file1, $file2, $file3, $file4,
        ]);
        $this->subject->expects(self::once())->method('findMatchedImage')->willReturn(null);

        $file1->expects(self::once())->method('getPublicUrl')->willReturn('file1');
        self::assertEquals('file1', $this->subject->getUrl());
    }
}
