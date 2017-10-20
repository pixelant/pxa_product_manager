<?php

namespace Pixelant\PxaProductManager\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;

/**
 * Class AjaxProductsController
 * @package Pixelant\PxaProductManager\Controller
 */
class AjaxProductsController extends ProductController
{
    /**
     * Initialize configuration
     */
    public function initializeAjaxLazyListAction()
    {
        // allow to create Demand from arguments
        $allowedProperties = GeneralUtility::trimExplode(
            ',',
            $this->settings['demand']['allowMapingProperties']
        );

        /** @var PropertyMappingConfiguration $demandConfiguration */
        $demandConfiguration = $this->arguments['demand']->getPropertyMappingConfiguration();
        $demandConfiguration->allowProperties(...$allowedProperties);
    }

    /**
     * Ajax lazy loading
     *
     * @param Demand $demand
     * @return string Json formatted string
     */
    public function ajaxLazyListAction(Demand $demand)
    {
        if ($this->settings['orderByAllowed']) {
            $demand->setOrderByAllowed($this->settings['orderByAllowed']);
        }

        // Raw result is much faster for ajax ?
        $products = $this->productRepository->findDemanded($demand);

        $productPid = $this->request->hasArgument('pagePid') ?
            (int)$this->request->getArgument('pagePid') : 0;

        if ($productPid && $productPid !== (int)$this->settings['pagePid']) {
            $this->settings['pagePid'] = $productPid;
            // Assign modified value from flexform
            $this->view->assign('settings', $this->settings);
        }

        // Get available options if required
        try {
            if ((int)$this->request->getArgument('hideFilterOptionsNoResult') === 1) {
                // @codingStandardsIgnoreStart
                list($availableOptions, $availableCategories, $productsNoLimitCount) = $this->getAvailableFilterOptionsAndCountProductFromDemand(
                    $demand
                );
                // @codingStandardsIgnoreEnd
            }
        } catch (NoSuchArgumentException $exception) {
            // just show all available options
        }

        $countResults = $productsNoLimitCount ?? $this->countDemanded($demand);
        $stopLoading = ($demand->getLimit() === 0 || ($demand->getLimit() + $demand->getOffSet() >= $countResults));

        $this->view->assign('products', $products);

        $this->response->setHeader('Content-Type', 'application/json');

        $response = [
            'lazyLoadingStop' => $stopLoading,
            'countResults' => $countResults,
            'availableOptionsList' => implode(',', $availableOptions ?? []),
            'availableCategoriesList' => implode(',', $availableCategories ?? []),
            'html' => $this->view->render()
        ];

        return json_encode($response);
    }
}
