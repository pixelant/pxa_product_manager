<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\Repository;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class FilterConstraints
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
     * @var array
     */
    protected array $constraints;

    /**
     * @param DemandInterface $demand
     * @param QueryInterface $query
     * @param array $constraints
     */
    public function __construct(DemandInterface $demand, QueryInterface $query, array $constraints)
    {
        $this->demand = $demand;
        $this->query = $query;
        $this->constraints = $constraints;
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

    /**
     * @return array
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param array $constraints
     * @return FilterConstraints
     */
    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;

        return $this;
    }
}
