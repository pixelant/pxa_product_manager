<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Property\TypeConverter\FileReferenceConverter;

/**
 * Set files for attribute.
 */
class FalMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(Product $product, AttributeValue $attributeValue): void
    {
        if (!empty($attributeValue)) {
            $fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
            $fileObjects = $fileRepository->findByRelation(
                'tx_pxaproductmanager_domain_model_attributevalue',
                'value',
                $attributeValue->getUid()
            );

            if (!empty($fileObjects)) {
                $files = [];

                /** @var FileReferenceConverter $fileReferenceConverter */
                $fileReferenceConverter = GeneralUtility::makeInstance(FileReferenceConverter::class);

                /** @var TYPO3\CMS\Core\Resource\FileReference $file */
                foreach ($fileObjects as $key => $file) {
                    $files[] = $fileReferenceConverter->convertFrom($file->getUid(), FileReference::class);
                }

                $attributeValue->setArrayValue($files);
            }
        }
    }

    /**
     * Same functionality as map() but exclude Extbase entities.
     *
     * @param int $attributeValue
     * @return array
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    public function mapToArray(int $attributeValue): array
    {
        $fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
        $fileObjects = $fileRepository->findByRelation(
            'tx_pxaproductmanager_domain_model_attributevalue',
            'value',
            $attributeValue
        );

        if (!empty($fileObjects)) {
            $files = [];

            /** @var FileReferenceConverter $fileReferenceConverter */
            $fileReferenceConverter = GeneralUtility::makeInstance(FileReferenceConverter::class);

            /** @var TYPO3\CMS\Core\Resource\FileReference $file */
            foreach ($fileObjects as $key => $file) {
                $files[] = $fileReferenceConverter->convertFrom($file->getUid(), FileReference::class);
            }

            return $files;
        }

        return [];
    }
}
