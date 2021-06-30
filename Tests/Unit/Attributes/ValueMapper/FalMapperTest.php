<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference as CoreFileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;
use TYPO3\CMS\Extbase\Property\TypeConverter\FileReferenceConverter;

class FalMapperTest extends UnitTestCase
{
    protected $resetSingletonInstances = true;

    /**
     * @test
     */
    public function mapWillSetMatchingFilesAsValueOfAttribute(): void
    {
        $fr2 = $this->prepareFixture(110, 2, 20);
        $fr3 = $this->prepareFixture(120, 3, 20);

        $efr2 = $this->prepareExtbaseFixture(110, 1, 20);
        $efr3 = $this->prepareExtbaseFixture(120, 1, 20);

        $fileRepository = $this->prophesize(FileRepository::class);
        $fileReferenceConverter = $this->prophesize(FileReferenceConverter::class);

        $fileRepository
            ->findByRelation('tx_pxaproductmanager_domain_model_attributevalue', 'value', 20)
            ->shouldBeCalled()
            ->willReturn([$fr2, $fr3]);

        $fileReferenceConverter
            ->convertFrom(110, ExtbaseFileReference::class)
            ->shouldBeCalled()
            ->willReturn(
                $efr2
            );

        $fileReferenceConverter
            ->convertFrom(120, ExtbaseFileReference::class)
            ->shouldBeCalled()
            ->willReturn(
                $efr3
            );

        GeneralUtility::setSingletonInstance(FileRepository::class, $fileRepository->reveal());
        GeneralUtility::setSingletonInstance(FileReferenceConverter::class, $fileReferenceConverter->reveal());

        /** @var Product $product */
        $product = TestsUtility::createEntity(Product::class, 1);

        /** @var Attribute $attribute */
        $attribute = TestsUtility::createEntity(Attribute::class, 10);
        $attribute->setType(Attribute::ATTRIBUTE_TYPE_FILE);

        /** @var AttributeValue $attributeValue */
        $attributeValue = TestsUtility::createEntity(AttributeValue::class, 20);
        $attributeValue->setAttribute($attribute);
        $attributeValue->setProduct($product);
        $attributeValue->setValue('2');

        self::assertEquals([$efr2, $efr3], $attributeValue->getArrayValue());
    }

    /**
     * @param int $fileReferenceUid
     * @param int $fileUid
     * @param int $attributeValueUid
     * @return \TYPO3\CMS\Core\Resource\FileReference|\PHPUnit\Framework\MockObject\MockObject|\TYPO3\TestingFramework\Core\AccessibleObjectInterface
     */
    protected function prepareFixture(int $fileReferenceUid, int $fileUid, int $attributeValueUid)
    {
        $fileReferenceProperties = [
            'uid' => $fileReferenceUid,
            'title' => 'fileTitle ' . $fileReferenceUid,
            'description' => 'fileReferenceDescription' . $fileReferenceUid,
            'uid_local' => $fileUid,
            'uid_foreign' => $attributeValueUid,
            'tablenames' => 'tx_pxaproductmanager_domain_model_attributevalue',
            'fieldname' => 'value',
            'alternative' => '',
            'file_only_property' => 'fileOnlyPropertyValue',
        ];

        $originalFileProperties = [
            'uid' => $fileUid,
            'title' => 'fileTitle' . $fileUid,
            'description' => 'fileDescription' . $fileUid,
            'alternative' => 'fileAlternative' . $fileUid,
            'file_only_property' => 'fileOnlyPropertyValue',
        ];

        $fixture = $this->getAccessibleMock(CoreFileReference::class, ['dummy'], [], '', false);
        $originalFileMock = $this->getAccessibleMock(File::class, [], [], '', false);
        $originalFileMock->expects(self::any())
            ->method('getProperties')
            ->willReturn(
                $originalFileProperties
            );
        $fixture->_set('originalFile', $originalFileMock);
        $fixture->_set('propertiesOfFileReference', $fileReferenceProperties);

        return $fixture;
    }

    /**
     * @param int $fileReferenceUid
     * @param int $fileUid
     * @param int $attributeValueUid
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|\PHPUnit\Framework\MockObject\MockObject|\TYPO3\TestingFramework\Core\AccessibleObjectInterface
     */
    protected function prepareExtbaseFixture(int $fileReferenceUid, int $fileUid, int $attributeValueUid)
    {
        $coreFileResource = $this->prepareFixture($fileReferenceUid, $fileUid, $attributeValueUid);

        $fixture = $this->getAccessibleMock(ExtbaseFileReference::class, ['dummy'], [], '', false);
        $fixture->_set('originalResource', $coreFileResource);

        return $fixture;
    }
}
