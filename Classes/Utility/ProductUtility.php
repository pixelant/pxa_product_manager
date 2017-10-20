<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Pavlo Zaporozkyi <pavlo@pixelant.se>, Pixelant
 *
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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProductUtility
 * @package Pixelant\PxaProductManager\Utility
 */
class ProductUtility
{
    /**
     * Store compare list with this key
     */
    const COMPARE_LIST_SESSION_NAME = 'tx_pxaproductmanager_compare_uids';

    /**
     * Name of cookie that keep latest visited product
     */
    const LATEST_VISITED_COOKIE_NAME = 'pxa_pm_latest_visited';

    /**
     * Name of cookie that keep wish list
     */
    const WISH_LIST_COOKIE_NAME = 'pxa_pm_wish_list';

    /**
     * Get array of uids of categories for product
     *
     * @param $product
     * @return array
     */
    public static function getProductCategoriesUids(int $product): array
    {
        $result = [];

        if ($product) {
            $configuration =
                $GLOBALS['TCA']['tx_pxaproductmanager_domain_model_product']['columns']['categories']['config'];

            /** @var RelationHandler $relationHandler */
            $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
            $relationHandler->start(
                '',
                'sys_category',
                'sys_category_record_mm',
                $product,
                'tx_pxaproductmanager_domain_model_product',
                $configuration
            );

            foreach ($relationHandler->itemArray as $item) {
                if ($item['id'] > 0) {
                    $result[] = $item['id'];
                }
            }
        }

        return $result;
    }

    /**
     * Get storage for plugin record
     *
     * @param int $pluginUid
     * @return string
     */
    public static function getStoragePidForPlugin(int $pluginUid): string
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        /** @noinspection PhpParamsInspection */
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $record = $queryBuilder
            ->select('pages', 'recursive')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($pluginUid, \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetch();

        if (!empty($record['pages'])) {
            $recursive = (int)$record['recursive'];

            if ($recursive <= 0) {
                return $record['pages'];
            }

            /** @var QueryGenerator $queryGenerator */
            $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
            $recursiveStoragePids = $record['pages'];

            $storagePids = GeneralUtility::intExplode(',', $record['pages'], true);
            foreach ($storagePids as $startPid) {
                $pids = $queryGenerator->getTreeList($startPid, $recursive, 0, 1);
                if (!empty($pids)) {
                    $recursiveStoragePids .= ',' . $pids;
                }
            }

            return $recursiveStoragePids;
        }


        return '';
    }

    /**
     * Uids of wish list
     *
     * @return array
     */
    public static function getWishList(): array
    {
        $list = $_COOKIE[self::WISH_LIST_COOKIE_NAME] ?: '';

        return GeneralUtility::intExplode(',', $list);
    }

    /**
     * Check if product in wish list
     *
     * @param object|int $product
     * @return bool
     */
    public static function isProductInWishList($product): bool
    {
        $list = $_COOKIE[self::WISH_LIST_COOKIE_NAME] ?: '';

        return GeneralUtility::inList($list, is_object($product) ? $product->getUid() : (int)$product);
    }
}
