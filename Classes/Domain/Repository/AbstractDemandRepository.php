<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

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

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Event\Repository\RepositoryDemand as RepositoryDemandEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use UnexpectedValueException;

/**
 * Class AbstractDemandRepository
 * @package Pixelant\PxaProductManager\Domain\Repository
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
    public function injectDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param DemandInterface $demand
     * @return QueryResultInterface
     */
    public function findDemanded(DemandInterface $demand): QueryResultInterface
    {
        return $this->createDemandQuery($demand)->execute();
    }

    /**
     * Count results for demand
     *
     * @param DemandInterface $demand
     * @return int
     */
    public function countByDemand(DemandInterface $demand): int
    {
        return $this->createDemandQuery($demand)->count();
    }

    /**
     * Prepare query
     *
     * @param DemandInterface $demand
     * @return QueryInterface
     */
    public function createDemandQuery(DemandInterface $demand): QueryInterface
    {
        $query = $this->createQuery();

        $this->fireDemandEvent('afterDemandQueryInitialize', $demand, $query);

        $this->setStorage($query, $demand);
        $this->setOrderings($query, $demand);

        if ($demand->getLimit()) {
            $query->setLimit($demand->getLimit());
        }
        if ($demand->getOffSet()) {
            $query->setOffset($demand->getOffSet());
        }

        $this->fireDemandEvent('beforeDemandCreateConstraints', $demand, $query);

        $constraints = $this->createConstraints($query, $demand);

        $this->fireDemandEvent('afterDemandCreateConstraints', $demand, $query);

        if (! empty($constraints)) {
            $query->matching(
                $this->createConstraintFromConstraintsArray(
                    $query,
                    $constraints,
                    'and'
                )
            );
        }

        $this->fireDemandEvent('beforeReturnDemandQuery', $demand, $query);

        return $query;
    }

    /**
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @return void
     */
    protected function setOrderings(QueryInterface $query, DemandInterface $demand)
    {
        if ($demand->getOrderBy()
            && GeneralUtility::inList($demand->getOrderByAllowed(), $demand->getOrderBy())
        ) {
            switch (strtolower($demand->getOrderDirection())) {
                case 'desc':
                    $orderDirection = QueryInterface::ORDER_DESCENDING;
                    break;
                default:
                    $orderDirection = QueryInterface::ORDER_ASCENDING;
            }

            $query->setOrderings([$demand->getOrderBy() => $orderDirection]);
        }
    }

    /**
     * Check if array consist from more than one constraint
     *
     * @param QueryInterface $query
     * @param array $constraints
     * @param string $conjunction
     * @return ConstraintInterface
     */
    protected function createConstraintFromConstraintsArray(
        QueryInterface $query,
        array $constraints,
        string $conjunction
    ): ConstraintInterface {
        if (empty($constraints)) {
            throw new UnexpectedValueException('Constraints array could not be empty', 1501051836879);
        }

        if (count($constraints) === 1) {
            return array_shift($constraints);
        }

        return $this->isOrConjunction($conjunction)
            ? $query->logicalOr($constraints)
            : $query->logicalAnd($constraints);
    }

    /**
     * Set storage if set
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     */
    protected function setStorage(QueryInterface $query, DemandInterface $demand)
    {
        if ($storage = $demand->getStoragePid()) {
            $storage = array_map('intval', $storage);

            $query->getQuerySettings()->setStoragePageIds($storage);
        }
    }

    /**
     * @param string $conjunction
     * @return bool
     */
    protected function isOrConjunction(string $conjunction): bool
    {
        return $conjunction === 'or';
    }

    /**
     * @param string $name
     * @param DemandInterface $demand
     * @param QueryInterface $query
     */
    protected function fireDemandEvent(string $name, DemandInterface $demand, QueryInterface $query): void
    {
        $event = GeneralUtility::makeInstance(RepositoryDemandEvent::class, $demand, $query);
        $this->dispatcher->dispatch(get_class($this), $name, [$event]);
    }

    /**
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @return array
     */
    abstract protected function createConstraints(QueryInterface $query, DemandInterface $demand): array;
}
