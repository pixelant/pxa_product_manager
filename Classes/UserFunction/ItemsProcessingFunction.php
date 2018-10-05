<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use TYPO3\CMS\Core\Category\Collection\CategoryCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ItemsProcessingFunction
 * @package Pixelant\PxaProductManager\UserFunction
 */
class ItemsProcessingFunction
{
    /**
     *
     * @param array &$data
     * @param TcaSelectItems $pObj
     */
    public function getListOfProductsWithinCategories(array &$data, TcaSelectItems $pObj)
    {
        if (!empty($data['row']['settings.customProductsList.productsCategories'])) {
            $items = &$data['items'];
            $products = [];

            $categories = GeneralUtility::intExplode(
                ',',
                $data['row']['settings.customProductsList.productsCategories'],
                true
            );
            foreach ($categories as $category) {
                /** @var CategoryCollection $collection */
                $collection = CategoryCollection::load(
                    $category,
                    true,
                    'tx_pxaproductmanager_domain_model_product',
                    'categories'
                );
                if ($collection->count() > 0) {
                    $products = array_merge($products, $collection->getItems());
                }
            }

            foreach ($products as $product) {
                $items[] = [
                    $product['name'] ?: 'No title',
                    $product['uid']
                ];
            }
        }
    }
}
