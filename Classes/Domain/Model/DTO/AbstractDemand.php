<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO;

abstract class AbstractDemand implements DemandInterface
{
    /**
     * Array of PIDs storage.
     *
     * @var array
     */
    protected ?array $storagePid = null;

    /**
     * Limit query result.
     *
     * @var int
     */
    protected int $limit = 0;

    /**
     * Offset query result.
     *
     * @var int
     */
    protected int $offSet = 0;

    /**
     * @var string
     */
    protected string $orderBy = '';

    /**
     * @var string
     */
    protected string $orderDirection = '';

    /**
     * Fields that are allowed to oder by.
     *
     * @var string
     */
    protected string $orderByAllowed = '';

    /**
     * @return array|null
     */
    public function getStoragePid(): ?array
    {
        return $this->storagePid;
    }

    /**
     * @param array $storagePid
     * @return AbstractDemand
     */
    public function setStoragePid(?array $storagePid): self
    {
        $this->storagePid = $storagePid;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return AbstractDemand
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffSet(): int
    {
        return $this->offSet;
    }

    /**
     * @param int $offSet
     * @return AbstractDemand
     */
    public function setOffSet(int $offSet): self
    {
        $this->offSet = $offSet;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * @return AbstractDemand
     */
    public function setOrderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * @param string $orderDirection
     * @return AbstractDemand
     */
    public function setOrderDirection(string $orderDirection): self
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderByAllowed(): string
    {
        return $this->orderByAllowed;
    }

    /**
     * @param string $orderByAllowed
     * @return AbstractDemand
     */
    public function setOrderByAllowed(string $orderByAllowed): self
    {
        $this->orderByAllowed = $orderByAllowed;

        return $this;
    }
}
