<?php

namespace Pixelant\PxaProductManager\Controller;

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

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\FilterRepository;
use Pixelant\PxaProductManager\Domain\Repository\OrderConfigurationRepository;
use Pixelant\PxaProductManager\Domain\Repository\OrderRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Navigation\CategoriesNavigationTreeBuilder;
use Pixelant\PxaProductManager\Traits\ProductRecordTrait;
use Pixelant\PxaProductManager\Utility\CategoryUtility;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class AbstractController
 * @package Pixelant\PxaProductManager\Controller
 */
class AbstractController extends ActionController
{
    use ProductRecordTrait;

    /**
     * Product repository
     *
     * @var ProductRepository
     */
    protected $productRepository = null;

    /**
     * Filter repository
     *
     * @var FilterRepository
     */
    protected $filterRepository = null;

    /**
     * Category repository
     *
     * @var CategoryRepository
     */
    protected $categoryRepository = null;

    /**
     * @var OrderRepository
     */
    protected $orderRepository = null;

    /**
     * @var OrderConfigurationRepository
     */
    protected $orderConfigurationRepository = null;

    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher = null;

    /**
     * @param ProductRepository $productRepository
     */
    public function injectProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param FilterRepository $filterRepository
     */
    public function injectFilterRepository(FilterRepository $filterRepository)
    {
        $this->filterRepository = $filterRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param OrderRepository $orderRepository
     */
    public function injectOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param OrderConfigurationRepository $orderConfigurationRepository
     */
    public function injectOrderConfigurationRepository(OrderConfigurationRepository $orderConfigurationRepository)
    {
        $this->orderConfigurationRepository = $orderConfigurationRepository;
    }

    /**
     * @param Dispatcher $signalSlotDispatcher
     */
    public function injectDispatcher(Dispatcher $signalSlotDispatcher)
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * Get category
     *
     * @param int $category
     * @return Category|object
     */
    protected function determinateCategory($category = 0)
    {
        if ($category) {
            $categoryUid = $category;
        } elseif (!empty($this->settings['category'])) {
            $categoryUid = (int)$this->settings['category'];
        }

        $category = $this->categoryRepository->findByUid($categoryUid ?? 0);

        if ($category === null) {
            $this->addFlashMessage(
                'Couldn\'t determine category, please check your selection.',
                'Error',
                FlashMessage::ERROR
            );
        }

        return $category;
    }

    /**
     * Generate categories tree
     *
     * @return array
     */
    protected function getNavigationTree()
    {
        $activeCategory = MainUtility::getActiveCategoryFromRequest();
        $excludeCategories = GeneralUtility::intExplode(
            ',',
            $this->settings['excludeCategories'],
            true
        );

        /** @var CategoriesNavigationTreeBuilder $treeBuilder */
        $treeBuilder = GeneralUtility::makeInstance(CategoriesNavigationTreeBuilder::class);

        $treeBuilder
            ->setExpandAll((bool)$this->settings['navigationExpandAll'])
            ->setHideCategoriesWithoutProducts((bool)$this->settings['navigationHideCategoriesWithoutProducts'])
            ->setExcludeCategories($excludeCategories);

        // set custom order
        if (!empty($orderings = $this->getOrderingsForCategories())) {
            $treeBuilder->setOrderings($orderings);
        }

        return $treeBuilder->buildTree(
            (int)$this->settings['category'],
            $activeCategory
        );
    }

    /**
     * Generate root line array of demand categories
     *
     * @param array $allowedCategories
     * @param array $excludeCategories
     * @return array
     */
    protected function getDemandCategories(array $allowedCategories = [], array $excludeCategories = [])
    {
        $allowedCategories = CategoryUtility::getCategoriesRootLine(
            $allowedCategories
        );

        return array_diff($allowedCategories, $excludeCategories);
    }

    /**
     * Generate ordering array for categories
     *
     * @return array
     */
    protected function getOrderingsForCategories(): array
    {
        if ($this->settings['orderCategoriesBy'] && $this->settings['orderCategoriesDirection']) {
            switch (strtolower($this->settings['orderCategoriesDirection'])) {
                case 'desc':
                    $orderDirection = QueryInterface::ORDER_DESCENDING;
                    break;
                default:
                    $orderDirection = QueryInterface::ORDER_ASCENDING;
            }

            return [
                $this->settings['orderCategoriesBy'] => $orderDirection
            ];
        }

        return [];
    }

    /**
     * Translate label
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    protected function translate(string $key, array $arguments = null): string
    {
        return LocalizationUtility::translate($key, 'PxaProductManager', $arguments) ?? '';
    }

    /**
     * Find all available filtering options for demand and count result
     *
     * @param Demand $demand
     * @return array
     */
    protected function getAvailableFilterOptionsAndCountProductFromDemand(Demand $demand)
    {
        $demandNoLimit = clone $demand;
        $demandNoLimit->setLimit(0);
        $demandNoLimit->setOffset(0);
        $productsNoLimit = $this->productRepository->findDemandedRaw($demandNoLimit);

        list($availableOptions, $availableCategories) = $this->getAvailableFilteringOptionsForProducts(
            $productsNoLimit
        );

        $productsNoLimitCount = count($productsNoLimit);
        unset($productsNoLimit, $demandNoLimit);

        return [$availableOptions, $availableCategories, $productsNoLimitCount];
    }

    /**
     * Remove options without products.
     *
     * @param array $products Raw data of products from DB
     * @return array
     */
    protected function getAvailableFilteringOptionsForProducts($products): array
    {
        $productUids = [];
        $attributeUids = [];
        $availableOptions = [];

        foreach ($products as &$product) {
            $productUids[] = $product['uid'];
            $attributeValues = $this->getAttributesValuesFromRow($product);

            if (!empty($attributeValues)) {
                $attributeUids = array_merge(
                    $attributeUids,
                    array_keys($attributeValues)
                );
                // Save unserialized
                $product['attributesValues'] = $attributeValues;
            }
        }
        $attributeUids = $this->filterOutAttributeUidsNotDropDown(array_unique($attributeUids));

        foreach ($products as $product) {
            if ($product['attributesValues']) {
                foreach ($product['attributesValues'] as $attributeUid => $attributeValue) {
                    if (in_array($attributeUid, $attributeUids, true)) {
                        // save option uid
                        $availableOptions = array_merge(
                            $availableOptions,
                            GeneralUtility::intExplode(',', $attributeValue, true)
                        );
                    }
                }
            }
        }

        $availableCategories = $this->categoryRepository->getProductsCategoriesUids($productUids);
        $availableOptions = array_unique($availableOptions);

        return [$availableOptions, $availableCategories];
    }

    /**
     * Filter attribute uids by dropdown type
     *
     * @param array $attributeUids
     * @return array
     */
    protected function filterOutAttributeUidsNotDropDown(array $attributeUids): array
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_pxaproductmanager_domain_model_attribute'
        );

        /** @noinspection PhpParamsInspection */
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $statement = $queryBuilder
            ->select('uid')
            ->from('tx_pxaproductmanager_domain_model_attribute')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq(
                        'type',
                        $queryBuilder->createNamedParameter(
                            Attribute::ATTRIBUTE_TYPE_DROPDOWN,
                            Connection::PARAM_INT
                        )
                    ),
                    $queryBuilder->expr()->eq(
                        'type',
                        $queryBuilder->createNamedParameter(
                            Attribute::ATTRIBUTE_TYPE_MULTISELECT,
                            Connection::PARAM_INT
                        )
                    )
                ),
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter(
                        $attributeUids,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->execute();

        $rows = [];
        while ($row = $statement->fetch()) {
            $rows[] = $row['uid'];
        }

        return $rows;
    }

    /**
     * Add labels for JS
     *
     * @return void
     */
    protected function getFrontendLabels()
    {
        static $jsLabelsAdded;

        if ($jsLabelsAdded === null) {
            $labelsJs = [];
            if (is_array($this->settings['translateJsLabels'])) {
                foreach ($this->settings['translateJsLabels'] as $translateJsLabelSet) {
                    $translateJsLabels = GeneralUtility::trimExplode(',', $translateJsLabelSet, true);
                    foreach ($translateJsLabels as $translateJsLabel) {
                        $labelsJs[$translateJsLabel] = $this->translate($translateJsLabel);
                    }
                }
            }
            if (!empty($labelsJs)) {
                $this->getPageRenderer()->addInlineLanguageLabelArray($labelsJs);
            }

            $jsLabelsAdded = true;
        }
    }

    /**
     * Sort query result according to uid list order
     *
     * @param QueryResultInterface $queryResults
     * @param array $uidList
     * @return array
     */
    protected function sortQueryResultsByUidList(QueryResultInterface $queryResults, array $uidList): array
    {
        $result = [];

        foreach ($queryResults as $queryResult) {
            $uid = ObjectAccess::getProperty($queryResult, 'uid');
            $result[$uid] = $queryResult;
        }

        if (!empty($result)) {
            $uidList = array_intersect($uidList, array_keys($result));
            // sort to have same order as in list
            $result = array_replace(array_flip($uidList), $result);
        }

        return $result;
    }

    /**
     * @return object|PageRenderer
     */
    protected function getPageRenderer()
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}
