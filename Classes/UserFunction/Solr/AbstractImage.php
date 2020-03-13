<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\Solr;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/**
 * @package Pixelant\PxaProductManager\UserFunction\Solr
 */
abstract class AbstractImage
{
    use CanCreateCollection;

    /**
     * @var ContentObjectRenderer
     */
    public ContentObjectRenderer $cObj;

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        // Collect all images
        $images = $this->getImages();
        if (empty($images)) {
            return '';
        }

        if ($matchedImage = $this->findMatchedImage($images)) {
            return $matchedImage->getPublicUrl();
        }

        return $images[0]->getPublicUrl();
    }

    /**
     * Find image by type
     *
     * @param array $images
     * @return FileReference|null
     */
    protected function findMatchedImage(array $images): ?FileReference
    {
        // Try to find by type
        $matchedImages = array_filter(
            $images,
            fn(FileReference $reference) => $reference->getReferenceProperty('pxapm_type') === $this->type()
        );

        if (!empty($matchedImages)) {
            return $matchedImages[0];
        }

        return null;
    }

    /**
     * Product images
     *
     * @return FileReference[]
     */
    protected function getImages(): array
    {
        $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
        $fileCollector->addFilesFromRelation(
            'tx_pxaproductmanager_domain_model_product',
            'images',
            $this->cObj->data
        );

        return $fileCollector->getFiles();
    }

    /**
     * Return type of product image
     *
     * @return int
     */
    abstract public function type(): int;
}
