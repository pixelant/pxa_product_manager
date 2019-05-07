<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO;

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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Class Demand
 * @package Pixelant\PxaProductManager\Domain\Model
 */
class Demand implements DemandInterface
{
    /**
     * Array of PIDs storage
     *
     * @var array
     */
    protected $storagePid = [];

    /**
     * Limit query result
     *
     * @var int
     */
    protected $limit = 0;

    /**
     * Offset query result
     *
     * @var int
     */
    protected $offSet = 0;

    /**
     * @var string
     */
    protected $orderBy = 'name';

    /**
     * @var string
     */
    protected $orderDirection = QueryInterface::ORDER_DESCENDING;

    /**
     * Fields that are allowed to oder by
     *
     * @var string
     */
    protected $orderByAllowed = '';

    /**
     * Array of uids
     *
     * @var array
     */
    protected $categories = [];

    /**
     * Array of uids
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Filters conjunction
     * could be 'or'
     *
     * @var string
     */
    protected $filtersConjunction = 'and';

    /**
     * Category conjunction
     * could be 'and'
     *
     * @var string
     */
    protected $categoryConjunction = 'or';

    /**
     * Include discontinued products
     *
     * @var bool
     */
    protected $includeDiscontinued = false;

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories(array $categories)
    {
        $this->categories = array_map('intval', $categories);
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
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
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
     */
    public function setOffSet(int $offSet)
    {
        $this->offSet = $offSet;
    }

    /**
     * @return string
     */
    public function getCategoryConjunction(): string
    {
        return $this->categoryConjunction;
    }

    /**
     * @param string $categoryConjunction
     */
    public function setCategoryConjunction(string $categoryConjunction)
    {
        $this->categoryConjunction = $categoryConjunction;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return string
     */
    public function getFiltersConjunction(): string
    {
        return $this->filtersConjunction;
    }

    /**
     * @param string $filtersConjunction
     */
    public function setFiltersConjunction(string $filtersConjunction)
    {
        $this->filtersConjunction = $filtersConjunction;
    }

    /**
     * @param array $filter
     */
    public function addFilter(array $filter)
    {
        $this->filters[] = $filter;
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
     */
    public function setOrderBy(string $orderBy)
    {
        $this->orderBy = $orderBy;
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
     */
    public function setOrderDirection(string $orderDirection)
    {
        $this->orderDirection = $orderDirection;
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
     */
    public function setOrderByAllowed(string $orderByAllowed)
    {
        $this->orderByAllowed = $orderByAllowed;
    }

    /**
     * @return array
     */
    public function getStoragePid(): array
    {
        return $this->storagePid;
    }

    /**
     * @param array $storagePid
     */
    public function setStoragePid(array $storagePid)
    {
        $this->storagePid = $storagePid;
    }

    /**
     * @return bool
     */
    public function getIncludeDiscontinued(): bool
    {
        return $this->includeDiscontinued;
    }

    /**
     * @param bool $includeDiscontinued
     */
    public function setIncludeDiscontinued(bool $includeDiscontinued)
    {
        $this->includeDiscontinued = $includeDiscontinued;
    }
}
