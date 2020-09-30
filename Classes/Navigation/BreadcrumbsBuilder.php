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

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
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
     * @var LinkBuilderService
     */
    protected $linkBuilder = null;

    /**
     * Initialize repositories
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->productRepository = $objectManager->get(ProductRepository::class);
        $this->categoryRepository = $objectManager->get(CategoryRepository::class);
        $this->linkBuilder = GeneralUtility::makeInstance(LinkBuilderService::class);
    }

    /**
     * Generate breadcrumbs
     *
     * @param string $content
     * @param array $configuration
     * @return array
     */
    public function buildBreadcrumbs(
        string $content,
        array $configuration
    ): array {
        $breadcrumbs = [];
        $arguments = GeneralUtility::_GP('tx_pxaproductmanager_pi1');

        if (is_array($arguments)) {
            /** @var LanguageAspect $languageAspect */
            $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
            $languageId = $languageAspect->getId();

            foreach ($arguments as $argument => $value) {
                if (StringUtility::beginsWith($argument, LinkBuilderService::CATEGORY_ARGUMENT_START_WITH)) {
                    /** @var Category $category */
                    $value = (int)$value;
                    $category = $this->categoryRepository->findByUid($value);
                    if ($category !== null && !$category->isNavHide()) {
                        $breadcrumbs[] = [
                            'title' => $category->getAlternativeTitle() ?: $category->getTitle(),
                            'product_uid' => $value,
                            'sys_language_uid' => $languageId,
                            '_OVERRIDE_HREF' => $this->buildLink($breadcrumbs, $value)
                        ];
                    }
                }
            }
            if ($product = (int)$arguments['product']) {
                /** @var Product $product */
                $product = $this->productRepository->findByUid($product);
            }

            if (is_object($product)) {
                $url = $this->buildLink($breadcrumbs, $product->getUid(), true);

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
                $parameters[LinkBuilderService::CATEGORY_ARGUMENT_START_WITH . $i++] = $breadcrumb['uid'];
            }
        }

        // now add actual parameter
        $key = $isProduct ? 'product' : LinkBuilderService::CATEGORY_ARGUMENT_START_WITH . $i;
        $parameters[$key] = $value;

        return $this->linkBuilder->buildForArguments((int)MainUtility::getTSFE()->id, $parameters);
    }
}
