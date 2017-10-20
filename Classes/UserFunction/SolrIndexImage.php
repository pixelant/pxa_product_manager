<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
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
     * @param string $content
     * @param array $params
     * @return string
     */
    public function getProductThumbnailImagePublicUrl(
        /** @noinspection PhpUnusedParameterInspection */ string $content,
        array $params
    ): string {
        /** @var FileReference $image */
        if ($image = $this->getProductImage($params, 'pxapm_use_in_listing')) {
            return $image->getPublicUrl();
        }

        return '';
    }

    /**
     * Get public url of main product image
     *
     * @param string $content
     * @param array $params
     * @return string
     */
    public function getProductMainImagePublicUrl(
        /** @noinspection PhpUnusedParameterInspection */ string $content,
        array $params
    ): string {
        /** @var FileReference $image */
        if ($image = $this->getProductImage($params, 'pxapm_main_image')) {
            return $image->getPublicUrl();
        }

        return '';
    }

    /**
     * Get reference uid of thumbnail product image
     *
     * @param string $content
     * @param array $params
     * @return int
     */
    public function getProductThumbnailImageReferenceUid(
        /** @noinspection PhpUnusedParameterInspection */ string $content,
        array $params
    ): int {
        /** @var FileReference $image */
        if ($image = $this->getProductImage($params, 'pxapm_use_in_listing')) {
            return $image->getUid();
        }

        return 0;
    }

    /**
     * Get reference uid of main product image
     *
     * @param string $content
     * @param array $params
     * @return int
     */
    public function getProductMainImageReferenceUid(
        /** @noinspection PhpUnusedParameterInspection */ string $content,
        array $params
    ): int {
        /** @var FileReference $image */
        if ($image = $this->getProductImage($params, 'pxapm_main_image')) {
            return $image->getUid();
        }

        return 0;
    }

    /**
     * Get product image
     *
     * @param array $params
     * @param string $imageField
     * @return object|null
     */
    protected function getProductImage(array $params, string $imageField)
    {
        /**
         * Example conf
         *  page.3 = USER
         *   page.3 {
         *       userFunc = Pixelant\PxaProductManager\UserFunction\SolrIndexImage->getProductImageReferenceUid
         *   }
         */
        $uid = (int)$this->cObj->stdWrap('', $params['uid.']);
        if (!empty($images = $this->getProductImages($uid))) {
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
     * @param int $productUid
     * @return array
     */
    public function getProductImages(int $productUid): array
    {
        if ($productUid) {
            $rawRecord = $this->getRawRecord($productUid);

            /** @var FileCollector $fileCollector */
            $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
            $fileCollector->addFilesFromRelation('tx_pxaproductmanager_domain_model_product', 'images', $rawRecord);
            return $fileCollector->getFiles();
        }

        return [];
    }

    /**
     * Product raw record
     *
     * @param int $uid
     * @return mixed
     */
    protected function getRawRecord(int $uid)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_product');

        $rawRecord = $queryBuilder->select('*')
            ->from('tx_pxaproductmanager_domain_model_product')
            ->where(
                $queryBuilder->expr()
                    ->eq(
                        'uid',
                        $uid
                    )
            )
            ->execute()
            ->fetch();

        return $rawRecord;
    }
}
