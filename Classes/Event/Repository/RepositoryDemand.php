<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\Repository;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class RepositoryDemand
{
    /**
     * @var DemandInterface
     */
    protected DemandInterface $demand;

    /**
     * @var QueryBuilder
     */
    protected QueryBuilder $queryBuilder;

    /**
     * @param DemandInterface $demand
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(DemandInterface $demand, QueryBuilder $queryBuilder)
    {
        $this->demand = $demand;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return DemandInterface
     */
    public function getDemand(): DemandInterface
    {
        return $this->demand;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }
}
