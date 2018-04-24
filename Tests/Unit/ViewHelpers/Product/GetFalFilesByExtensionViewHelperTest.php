<?php

namespace Pixelant\PxaProductManager\Tests\Unit\ViewHelpers\Product;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\ViewHelpers\Product\GetFalFilesByExtensionViewHelper;
use TYPO3\CMS\Core\Resource\FileReference as FileReferenceOriginal;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class GetFalFilesByExtensionViewHelperTest extends UnitTestCase
{

    protected function createTestingProduct($limit = 0)
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $i = 0;

        foreach (['pdf', 'pdf', 'pdf', 'doc', 'docx'] as $extension) {
            $mockedFileReference = $this->getAccessibleMock(
                FileReference::class,
                ['dummy'],
                [],
                '',
                false
            );

            $mockedOriginalFile = $this->createPartialMock(
                FileReferenceOriginal::class,
                ['getExtension']
            );

            $mockedOriginalFile
                ->expects(($limit === 0 || $limit > $i) ? $this->once() : $this->never())
                ->method('getExtension')
                ->willReturn($extension);

            $mockedFileReference->_set('originalResource', $mockedOriginalFile);

            $objectStorage->attach($mockedFileReference);
            $i++;
        }

        $product->setFalLinks($objectStorage);

        return $product;
    }

    /**
     * @test
     */
    public function getFilesByExtensionNoLimit()
    {
        $product = $this->createTestingProduct();
        $arguments = [
            'product' => $product,
            'extension' => 'pdf'
        ];
        $mockedRenderingContextInterface = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $files = GetFalFilesByExtensionViewHelper::renderStatic(
            $arguments,
            $closure,
            $mockedRenderingContextInterface
        );

        $this->assertCount(
            3,
            $files
        );
    }

    /**
     * @test
     */
    public function getFilesByExtensionWithLimit()
    {
        $limit = 2;
        $product = $this->createTestingProduct($limit);
        $arguments = [
            'product' => $product,
            'extension' => 'pdf',
            'limit' => $limit
        ];
        $mockedRenderingContextInterface = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $files = GetFalFilesByExtensionViewHelper::renderStatic(
            $arguments,
            $closure,
            $mockedRenderingContextInterface
        );

        $this->assertCount(
            $limit,
            $files
        );
    }

    /**
     * @test
     */
    public function getFileByExtensionWhereOneExistReturnOneFile()
    {
        $product = $this->createTestingProduct();
        $arguments = [
            'product' => $product,
            'extension' => 'docx'
        ];
        $mockedRenderingContextInterface = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $files = GetFalFilesByExtensionViewHelper::renderStatic(
            $arguments,
            $closure,
            $mockedRenderingContextInterface
        );

        $this->assertCount(
            1,
            $files
        );
    }
}
