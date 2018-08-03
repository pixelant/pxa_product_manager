<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Exception\UnknownOrdersTabException;
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
     * Get order for tab
     *
     * @param string $tab
     * @param array $storage
     * @return QueryResultInterface
     * @throws UnknownOrdersTabException
     */
    public function getOrderForTab(string $tab, array $storage = []): QueryResultInterface
    {
        switch ($tab) {
            case 'active':
                return $this->findActive($storage);
            case 'complete':
                return $this->findCompleted($storage);
            case 'archive':
                return $this->findArchived($storage);
            default:
                throw new UnknownOrdersTabException('Tab "' . $tab . '" is not supported', 1532519130852);
        }
    }

    /**
     * Find all order in current root line
     *
     * @param array $customStorage
     * @return QueryResultInterface
     */
    public function findCompleted(array $customStorage = []): QueryResultInterface
    {
        $query = $this->createQuery();
        $this->setCustomStorage($query, $customStorage);

        $query->matching(
            $query->equals('complete', true)
        );

        return $query->execute();
    }

    /**
     * Find all archived orders in current root line
     * @param array $customStorage
     * @return QueryResultInterface
     */
    public function findArchived(array $customStorage = []): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setIgnoreEnableFields(true)
            ->setEnableFieldsToBeIgnored(['disabled']);

        $this->setCustomStorage($query, $customStorage);

        $query->matching(
            $query->equals('hidden', true)
        );

        return $query->execute();
    }

    /**
     * Find all un-completed
     *
     * @param array $customStorage
     * @return QueryResultInterface
     */
    public function findActive(array $customStorage = []): QueryResultInterface
    {
        $query = $this->createQuery();
        $this->setCustomStorage($query, $customStorage);

        $query->matching(
            $query->equals('complete', false)
        );

        return $query->execute();
    }

    /**
     * Find by id, even hidden
     *
     * @param int $uid
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
     * Override storage of query
     *
     * @param QueryInterface $query
     * @param array $customStorage
     */
    protected function setCustomStorage(QueryInterface $query, array $customStorage)
    {
        if (!empty($customStorage)) {
            $query
                ->getQuerySettings()
                ->setStoragePageIds($customStorage);
        }
    }
}
