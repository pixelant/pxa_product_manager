<?php

namespace Pixelant\PxaProductManager\Tests\Unit\ViewHelpers\Solr;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\ViewHelpers\Solr\ImageViewHelper;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * Class ImageViewHelperTest
 * @package Pixelant\PxaProductManager\Tests\Unit\ViewHelpers\Solr
 */
class ImageViewHelperTest extends UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface|ImageViewHelper
     */
    protected $viewHelper;

    protected function setUp()
    {
        $this->viewHelper = $this->getAccessibleMock(
            ImageViewHelper::class,
            ['dummy'],
            [],
            '',
            false
        );
    }

    /**
     * @test
     */
    public function getProcessedImageUrlForReferenceUidWillProcessImage()
    {
        $image = 1;
        $maxWidth = 250;
        $maxHeight = 300;
        $treadAsReference = true;

        $fileReference = $this->createPartialMock(FileReference::class, ['hasProperty']);
        $processedFile = $this->createMock(ProcessedFile::class);

        $imageService = $this->createPartialMock(
            ImageService::class,
            ['getImage', 'applyProcessingInstructions', 'getImageUri']
        );
        $this->viewHelper->_set('imageService', $imageService);

        $imageService->expects($this->once())
            ->method('getImage')
            ->with($image, null, $treadAsReference)
            ->willReturn($fileReference);
        $fileReference->expects($this->once())
            ->method('hasProperty')
            ->with('crop')
            ->willReturn(false);

        $processingInstructions = [
            'width' => null,
            'height' => null,
            'minWidth' => null,
            'minHeight' => null,
            'maxWidth' => $maxWidth,
            'maxHeight' => $maxHeight,
            'crop' => null,
        ];

        $imageService->expects($this->once())
            ->method('applyProcessingInstructions')
            ->with($fileReference, $processingInstructions)
            ->willReturn($processedFile);

        $imageService->expects($this->once())
            ->method('getImageUri')
            ->with($processedFile)
            ->willReturn('');

        $this->viewHelper->execute([$image, $maxWidth, $maxHeight]);
    }

    protected function tearDown()
    {
        unset($this->viewHelper);
    }
}
