<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

use Pixelant\PxaProductManager\Domain\Model\Order;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

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

/**
 * Class OrderRepository
 * @package Pixelant\PxaProductManager\Domain\Repository
 */
class OrderRepository extends Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'crdate' => QueryInterface::ORDER_DESCENDING
    );

    /**
     * Find all order in current root line
     *
     * @param int $pid
     * @return QueryResultInterface
     */
    public function findAllCompleted(int $pid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setStoragePageIds($this->getTreeListArrayForPid($pid))
            ->setRespectSysLanguage(false);

        $query->matching(
            $query->equals('complete', true)
        );

        return $query->execute();
    }

    /**
     * Find all archived orders in current root line
     * @param int $pid
     * @return QueryResultInterface
     */
    public function findAllArchivedInRootLine(int $pid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setIgnoreEnableFields(true)
            ->setEnableFieldsToBeIgnored(['disabled'])
            ->setStoragePageIds($this->getTreeListArrayForPid($pid))
            ->setRespectSysLanguage(false);

        $query->matching(
            $query->equals('hidden', true)
        );

        return $query->execute();
    }

    /**
     * Find all new orders for current BE user
     *
     * @param int $pid
     * @param BackendUser $backendUser
     * @return QueryResultInterface
     */
    public function findNewOrders(int $pid): QueryResultInterface
    {
        $query = $this->createQuery();

        $query->getQuerySettings()
            ->setStoragePageIds($this->getTreeListArrayForPid($pid))
            ->setRespectSysLanguage(false);

        $query->matching(
            $query->equals('complete', false)
        );

        return $query->execute();
    }

    /**
     * Find by id, even hidden
     *
     * @param int $pid
     * @return Order|null
     */
    public function findByIdIgnoreHidden(int $uid)
    {
        $query = $this->createQuery();

        $query->getQuerySettings()
            ->setIgnoreEnableFields(true)
            ->setEnableFieldsToBeIgnored(['disabled'])
            ->setRespectStoragePage(false)
            ->setRespectSysLanguage(false);

        $query->matching(
            $query->equals('uid', $uid)
        );

        return $query->execute()->getFirst();
    }

    /**
     * Get array of recursive pids
     *
     * @param int $pid
     * @return array
     */
    protected function getTreeListArrayForPid(int $pid): array
    {
        $queryGenerator = $this->objectManager->get(QueryGenerator::class);

        return GeneralUtility::intExplode(',', $queryGenerator->getTreeList($pid, 99, 0, 1));
    }
}
