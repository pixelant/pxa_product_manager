<?php

namespace Pixelant\PxaProductManager\Event\Repository;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class GetProductQueryBuilderEvent
{
    /**
     * @var QueryBuilder
     */
    protected QueryBuilder $queryBuilder;

    /**
     * DemandEvent constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @param QueryBuilder $settings
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }
}
