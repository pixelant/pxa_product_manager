<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\Repository;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class RepositoryDemand
{
    /**
     * @var DemandInterface
     */
    protected DemandInterface $demand;

    /**
     * @var QueryInterface
     */
    protected QueryInterface $query;

    /**
     * @param DemandInterface $demand
     * @param QueryInterface $query
     */
    public function __construct(DemandInterface $demand, QueryInterface $query)
    {
        $this->demand = $demand;
        $this->query = $query;
    }

    /**
     * @return DemandInterface
     */
    public function getDemand(): DemandInterface
    {
        return $this->demand;
    }

    /**
     * @return QueryInterface
     */
    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
