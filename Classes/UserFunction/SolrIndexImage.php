<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/**
 * Class SolrIndexImage
 * @package Pixelant\PxaProductManagerImport\UserFunction
 */
class SolrIndexImage
{
    /**
     * @var ContentObjectRenderer
     */
    public $cObj;

    /**
     * Get public url of thumbnail product image
     *
     * @return string
     */
    public function getProductThumbnailImagePublicUrl(): string
    {
        /** @var FileReference $image */
        if ($image = $this->getProductImage('pxapm_use_in_listing')) {
            return $image->getPublicUrl();
        }

        return '';
    }

    /**
     * Get public url of main product image
     *
     * @return string
     */
    public function getProductMainImagePublicUrl(): string
    {
        /** @var FileReference $image */
        if ($image = $this->getProductImage('pxapm_main_image')) {
            return $image->getPublicUrl();
        }

        return '';
    }

    /**
     * Get reference uid of thumbnail product image
     *
     * @return int
     */
    public function getProductThumbnailImageReferenceUid(): int
    {
        /** @var FileReference $image */
        if ($image = $this->getProductImage('pxapm_use_in_listing')) {
            return $image->getUid();
        }

        return 0;
    }

    /**
     * Get reference uid of main product image
     *
     * @return int
     */
    public function getProductMainImageReferenceUid(): int
    {
        /** @var FileReference $image */
        if ($image = $this->getProductImage('pxapm_main_image')) {
            return $image->getUid();
        }

        return 0;
    }

    /**
     * Get first image of product
     *
     * @return string
     */
    public function getProductFirstImage(): string
    {
        /** @var FileReference[] $images */
        $images = $this->getProductImages();

        if (!empty($images)) {
            return $images[0]->getPublicUrl();
        }

        return '';
    }

    /**
     * Get first image of product
     *
     * @return string
     */
    public function getAllProductImages(): string
    {
        $imagesPaths = [];
        foreach ($this->getProductImages() as $image) {
            $imagesPaths[] = $image->getPublicUrl();
        }

        return implode(',', $imagesPaths);
    }

    /**
     * Get product image
     *
     * @param string $imageField
     * @return FileReference|null
     */
    protected function getProductImage(string $imageField)
    {
        /**
         * Example conf
         *  page.3 = USER
         *   page.3 {
         *       userFunc = Pixelant\PxaProductManager\UserFunction\SolrIndexImage->getProductImageReferenceUid
         *   }
         */
        $images = $this->getProductImages();
        if (!empty($images)) {
            /** @var FileReference $image */
            foreach ($images as $image) {
                if ((bool)$image->getProperty($imageField)) {
                    return $image;
                }
            }

            return $images[0];
        }

        return null;
    }

    /**
     * Product images
     *
     * @return FileReference[]
     */
    public function getProductImages(): array
    {
        /** @var FileCollector $fileCollector */
        $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
        $fileCollector->addFilesFromRelation(
            'tx_pxaproductmanager_domain_model_product',
            'images',
            $this->cObj->data
        );
        return $fileCollector->getFiles();
    }
}
