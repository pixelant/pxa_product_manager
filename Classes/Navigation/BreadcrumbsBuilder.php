<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Navigation;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017
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

use Pixelant\PxaProductManager\Controller\NavigationController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class BreadcrumbsBuilder
 * @package Pixelant\PxaProductManager\Navigation
 */
class BreadcrumbsBuilder
{
    /**
     * @var ContentObjectRenderer
     */
    public $cObj;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Initialize repositories
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->productRepository = $objectManager->get(ProductRepository::class);
        $this->categoryRepository = $objectManager->get(CategoryRepository::class);
    }

    /**
     * Generate breadcrumbs
     *
     * @param string $content
     * @param array $configuration
     * @return array
     */
    public function buildBreadcrumbs(
        /** @noinspection PhpUnusedParameterInspection */ string $content,
        array $configuration
    ): array {
        $breadcrumbs = [];
        $arguments = GeneralUtility::_GP('tx_pxaproductmanager_pi1');

        if (is_array($arguments)) {
            foreach ($arguments as $argument => $value) {
                if (StringUtility::beginsWith($argument, NavigationController::CATEGORY_ARG_START_WITH)) {
                    /** @var Category $category */
                    $value = (int)$value;
                    $category = $this->categoryRepository->findByUid($value);
                    if ($category !== null) {
                        $breadcrumbs[] = [
                            'title' => $category->getAlternativeTitle() ?: $category->getTitle(),
                            'uid' => $value,
                            '_OVERRIDE_HREF' => $this->buildLink($breadcrumbs, $value)
                        ];
                    }
                }
            }
            $product = (int)$arguments['product'];

            if ($product) {
                /** @var Product $product */
                $product = $this->productRepository->findByUid($product);
                $url = $this->buildLink($breadcrumbs, $product->getUid(), true);

                // @codingStandardsIgnoreStart
                if ((int)$configuration['skipPostVarDefaultSegment'] === 0
                    && isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['product'])) {
                    // static postVar realurl
                    $breadcrumbs[] = [
                        'title' => 'product',
                        '_OVERRIDE_HREF' => $url,
                        'ITEM_STATE' => 'CUR'
                    ];
                }
                // @codingStandardsIgnoreEnd
                $breadcrumbs[] = [
                    'title' => $product->getAlternativeTitle() ?: $product->getName(),
                    '_OVERRIDE_HREF' => $url,
                    'ITEM_STATE' => 'CUR'
                ];
            } else {
                // make last category current state
                if (!empty($breadcrumbs)) {
                    $breadcrumbs[count($breadcrumbs) - 1]['ITEM_STATE'] = 'CUR';
                }
            }
        }

        return $breadcrumbs;
    }

    /**
     * Generate link for breadcrumbs
     *
     * @param array $breadcrumbs
     * @param int $value
     * @param bool $isProduct
     * @return string
     */
    protected function buildLink(array $breadcrumbs, int $value, bool $isProduct = false): string
    {
        $parameters = [];

        // add parameters before
        $i = 0;
        foreach ($breadcrumbs as $breadcrumb) {
            if ($breadcrumb['uid']) {
                $parameters[NavigationController::CATEGORY_ARG_START_WITH . $i++] = $breadcrumb['uid'];
            }
        }

        // now add actual parameter
        $key = $isProduct ? 'product' : NavigationController::CATEGORY_ARG_START_WITH . $i;
        $parameters[$key] = $value;

        return $this->cObj->getTypoLink_URL(
            MainUtility::getTSFE()->id,
            [
                'tx_pxaproductmanager_pi1' => $parameters
            ]
        );
    }
}
