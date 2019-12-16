<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

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

use Pixelant\PxaProductManager\Domain\Model\Category;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class CategoryRepository extends \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository
{
    /**
     * Default orderings
     *
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
        'title' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Get categories by uids list
     *
     * @param array $uids
     * @param bool $rawResult
     * @return array
     */
    public function findByUidList(array $uids, bool $rawResult = false): array
    {
        if (!empty($uids)) {
            $query = $this->createQuery();

            $query
                ->getQuerySettings()
                ->setRespectStoragePage(false)
                ->setRespectSysLanguage(false);

            $query->matching(
                $query->in('uid', $uids)
            );

            return $rawResult ? $query->execute(true) : $query->execute()->toArray();
        }

        return [];
    }

    /**
     * Get categories for some folder
     * Use in BE module to list categories
     *
     * @param int $pid
     * @return QueryResultInterface
     */
    public function findCategoriesByPidAndParentIgnoreHidden(int $pid, Category $category = null)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setIgnoreEnableFields(true)
            ->setEnableFieldsToBeIgnored(['disabled']);

        $query->matching(
            $query->logicalAnd([
                $query->equals('pid', $pid),
                $query->equals('parent', $category ?? 0)
            ])
        );

        $query->setOrderings([
            'sorting' => QueryInterface::ORDER_ASCENDING
        ]);

        return $query->execute();
    }

    /**
     * Get categories root line
     *
     * @param array $idList
     * @param bool $removeGivenIdListFromResult
     * @return array
     */
    public function getChildrenCategories(
        array $idList,
        bool $removeGivenIdListFromResult = false
    ): array {
        $categories = $this->getChildrenCategoriesRecursive(
            $idList
        );

        if ($removeGivenIdListFromResult) {
            return array_diff($categories, $idList);
        }

        return $categories;
    }

    /**
     * Find categories by parent category
     * This is mostly used for navigation, so we need possibility to set direction
     *
     * @param mixed $parentCategory
     * @param array $ordering
     * @return QueryResultInterface
     */
    public function findByParent($parentCategory, array $ordering = [])
    {
        $query = $this->createQuery();

        $query->matching($query->equals('parent', $parentCategory));
        if (!empty($ordering)) {
            $query->setOrderings($ordering);
        }

        return $query->execute();
    }

    /**
     * Get categories uids
     *
     * @param array $productsUids
     * @return array
     */
    public function getProductsCategoriesUids(array $productsUids): array
    {
        $categories = [];

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'sys_category_record_mm'
        );
        $queryBuilder->getRestrictions()->removeAll();

        $statement = $queryBuilder
            ->select('uid_local')
            ->from('sys_category_record_mm')
            ->where(
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter(
                        'tx_pxaproductmanager_domain_model_product',
                        Connection::PARAM_STR
                    )
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter(
                        'categories',
                        Connection::PARAM_STR
                    )
                ),
                $queryBuilder->expr()->in(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter(
                        $productsUids,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->execute();

        while ($uid = $statement->fetchColumn(0)) {
            $categories[] = $uid;
        }

        return array_values(array_unique($categories));
    }

    /**
     * Go go children of category
     *
     * @param array $categories
     * @param int $counter
     * @return array
     */
    private function getChildrenCategoriesRecursive(array $categories, int $counter = 0): array
    {
        $result = [];

        // add start categories to the output too
        if ($counter === 0) {
            $result += $categories;
        }

        $query = $this->createQuery();

        $query->matching(
            $query->in('parent', $categories)
        );

        $childrenCategories = $query->execute(true);

        if (!empty($childrenCategories)) {
            foreach ($childrenCategories as $childCategory) {
                $counter++;
                if ($counter > 10000) {
                    // loop
                    return $result;
                }
                $subcategories = $this->getChildrenCategoriesRecursive([$childCategory['uid']], $counter);
                $result = array_merge([$childCategory['uid']], $result, $subcategories);
            }
        }

        return $result;
    }

    /**
     * @param int $pageId
     * @return array|QueryResultInterface
     */
    public function findByRelatedToContentPage(int $pageId)
    {
        $query = $this->findAll()->getQuery();
        $results = $query->matching(
            $query->equals(
                'contentPage',
                $pageId
            )
        )->execute(true);

        return array_map(function ($result) {
            return [
                'uid' => $result['uid'],
                'title' => $result['title']
            ];
        }, $results);
    }
}
