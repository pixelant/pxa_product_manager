<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

/*
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
 */

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Event\Repository\RepositoryDemand as RepositoryDemandEvent;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Class AbstractDemandRepository.
 */
abstract class AbstractDemandRepository extends Repository implements DemandRepositoryInterface
{
    /**
     * @var Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function injectDispatcher(Dispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param DemandInterface $demand
     * @return array
     */
    public function findDemanded(DemandInterface $demand): array
    {
        $result = $this->createDemandQueryBuilder($demand)->execute();
        $dataMapper = $this->objectManager->get(DataMapper::class);

        return $dataMapper->map(
            $this->getObjectClassName(),
            $result->fetchAll()
        );
    }

    /**
     * Count results for demand.
     *
     * @param DemandInterface $demand
     * @return int
     */
    public function countByDemand(DemandInterface $demand): int
    {
        $queryBuilder = $this->createDemandQueryBuilder($demand);

        return $this->createDemandQuery($demand)->count();
    }

    /**
     * Add storage expression if set.
     *
     * @param QueryBuilder $queryBuilder
     * @param DemandInterface $demand
     */
    protected function addStorageExpression(QueryBuilder $queryBuilder, DemandInterface $demand): void
    {
        $storage = $demand->getStoragePid();
        if ($storage) {
            $storage = array_map('intval', $storage);

            $queryBuilder->where(
                $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter(
                        $storage,
                        \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY
                    )
                )
            );
        }
    }

    /**
     * Add limit if set.
     *
     * @param QueryBuilder $queryBuilder
     * @param DemandInterface $demand
     */
    protected function addLimit(QueryBuilder $queryBuilder, DemandInterface $demand): void
    {
        if ($demand->getLimit()) {
            $queryBuilder->setMaxResults($demand->getLimit());
        }
    }

    /**
     * Add offset if set.
     *
     * @param QueryBuilder $queryBuilder
     * @param DemandInterface $demand
     */
    protected function addOffset(QueryBuilder $queryBuilder, DemandInterface $demand): void
    {
        if ($demand->getOffSet()) {
            $queryBuilder->setFirstResult($demand->getOffSet());
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param DemandInterface $demand
     * @return void
     */
    protected function addOrderings(QueryBuilder $queryBuilder, DemandInterface $demand): void
    {
        if (
            $demand->getOrderBy()
            && GeneralUtility::inList($demand->getOrderByAllowed(), $demand->getOrderBy())
        ) {
            switch (strtolower($demand->getOrderDirection())) {
                case 'desc':
                    $orderDirection = QueryInterface::ORDER_DESCENDING;

                    break;
                default:
                    $orderDirection = QueryInterface::ORDER_ASCENDING;

                    break;
            }
            $queryBuilder->orderBy($demand->getOrderBy(), $orderDirection);
        }
    }

    /**
     * Set storage if set.
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     */
    protected function setStorage(QueryInterface $query, DemandInterface $demand): void
    {
        $storage = $demand->getStoragePid();
        if ($storage) {
            $storage = array_map('intval', $storage);

            $query->getQuerySettings()->setStoragePageIds($storage);
        }
    }

    /**
     * @param string $name
     * @param DemandInterface $demand
     * @param QueryBuilder $queryBuilder
     */
    protected function fireDemandEvent(string $name, DemandInterface $demand, QueryBuilder $queryBuilder): void
    {
        $event = GeneralUtility::makeInstance(RepositoryDemandEvent::class, $demand, $queryBuilder);
        $this->dispatcher->dispatch(get_class($this), $name, [$event]);
    }
}
