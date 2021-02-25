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

use Pixelant\Demander\Service\DemandService;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AbstractDemandRepository.
 */
abstract class AbstractDemandRepository extends Repository implements DemandRepositoryInterface
{
    /**
     * @var EventDispatcher
     */
    protected EventDispatcher $dispatcher;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function injectDispatcher(EventDispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param DemandService $demandService
     */
    public function injectDemandService(DemandService $demandService): void
    {
        $this->demandService = $demandService;
    }

    /**
     * Returns the demanded records.
     *
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
     * Add storage to querybuilder expression if set.
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
     * Add limit to querybuilder if set.
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
     * Add offset to querybuilder if set.
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
     * Add orderings to querybuilder if set.
     *
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
}
