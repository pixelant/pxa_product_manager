<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO;

use Pixelant\PxaProductManager\Domain\Model\Filter;

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

/**
 * Class Demand.
 */
class ProductDemand extends AbstractDemand
{
    /**
     * Page Tree Starting Point.
     *
     * @var int
     */
    protected int $pageTreeStartingPoint = 0;

    /**
     * Lazy loading filter conjunction.
     *
     * @var string
     */
    protected string $filterConjunction = 'and';

    /**
     * @var bool
     */
    protected bool $hideFilterOptionsNoResult = false;

    /**
     * Filters from lazy loading.
     *
     * @var array
     */
    protected array $filters = [];

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     * @return ProductDemand
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Remove filter by uid.
     *
     * @param int $uid
     * @return ProductDemand
     */
    public function removeFilter(int $uid): self
    {
        if (isset($this->filters[$uid])) {
            unset($this->filters[$uid]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFilterConjunction(): string
    {
        return $this->filterConjunction;
    }

    /**
     * @param string $filterConjunction
     * @return ProductDemand
     */
    public function setFilterConjunction(string $filterConjunction): self
    {
        $this->filterConjunction = $filterConjunction;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHideFilterOptionsNoResult(): bool
    {
        return $this->hideFilterOptionsNoResult;
    }

    /**
     * @param bool $hideFilterOptionsNoResult
     * @return ProductDemand
     */
    public function setHideFilterOptionsNoResult(bool $hideFilterOptionsNoResult): self
    {
        $this->hideFilterOptionsNoResult = $hideFilterOptionsNoResult;

        return $this;
    }

    /**
     * Return true if one of the filters is category filter.
     *
     * @return bool
     */
    public function hasFiltersCategoryFilter(): bool
    {
        foreach ($this->filters as $filter) {
            if ((int)$filter['type'] === Filter::TYPE_CATEGORIES && !empty($filter['value'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get page Tree Starting Point.
     *
     * @return int
     */
    public function getPageTreeStartingPoint()
    {
        return $this->pageTreeStartingPoint;
    }

    /**
     * Set page Tree Starting Point.
     *
     * @param int  $pageTreeStartingPoint  Page Tree Starting Point.
     * @return ProductDemand
     */
    public function setPageTreeStartingPoint(int $pageTreeStartingPoint)
    {
        $this->pageTreeStartingPoint = $pageTreeStartingPoint;

        return $this;
    }
}
