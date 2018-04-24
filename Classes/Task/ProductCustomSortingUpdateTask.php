<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Task;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Pixelant\PxaProductManager\Utility\ProductUtility;

/**
 * Class ProductCustomSortingUpdateTask
 * @package Pixelant\PxaProductManager\Task
 */
class ProductCustomSortingUpdateTask extends AbstractTask
{
    /**
     * Executes the Product Custom Sorting Update task, calclating custom sorting for each product.
     * Only needed if custom sorting is used and configured in TS.
     *
     * @see \TYPO3\CMS\Scheduler\Task\AbstractTask::execute()
     */
    public function execute()
    {
        $updatedProducts = 0;
        /** @var ProductRepository $productRepository */
        $productRepository = MainUtility::getObjectManager()->get(ProductRepository::class);

        $products = $productRepository->findAll(false);

        if ($products) {
            /** @var Product $product */
            foreach ($products as $product) {
                try {
                    $product->setCustomSorting(ProductUtility::getCalculatedCustomSorting($product));
                    if ($product->_isDirty()) {
                        $productRepository->update($product);
                        $updatedProducts++;
                    }
                } catch (\Exception $e) {
                    return false;
                }
            }
            if ($updatedProducts > 0) {
                /** @var PersistenceManager $persistenceManager */
                $persistenceManager = MainUtility::getObjectManager()->get(PersistenceManager::class);
                $persistenceManager->persistAll();
            }
        }
        return true;
    }
}
