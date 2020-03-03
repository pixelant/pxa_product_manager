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
class ProductDemand implements DemandInterface
{
    /**
     * Array of PIDs storage
     *
     * @var array|null
     */
    protected ?array $storagePid = null;

    /**
     * Limit query result
     *
     * @var int
     */
    protected int $limit = 0;

    /**
     * Offset query result
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
     * Fields that are allowed to oder by
     *
     * @var string
     */
    protected string $orderByAllowed = '';

    /**
     * Array of uids
     *
     * @var array
     */
    protected array $categories = [];

    /**
     * Category conjunction
     * could be 'and'
     *
     * @var string
     */
    protected string $categoryConjunction = 'or';

    /**
     * @return array|null
     */
    public function getStoragePid(): ?array
    {
        return $this->storagePid;
    }

    /**
     * @param array|null $storagePid
     * @return ProductDemand
     */
    public function setStoragePid(?array $storagePid): ProductDemand
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
     * @return ProductDemand
     */
    public function setLimit(int $limit): ProductDemand
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
     * @return ProductDemand
     */
    public function setOffSet(int $offSet): ProductDemand
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
     * @return ProductDemand
     */
    public function setOrderBy(string $orderBy): ProductDemand
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
     * @return ProductDemand
     */
    public function setOrderDirection(string $orderDirection): ProductDemand
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
     * @return ProductDemand
     */
    public function setOrderByAllowed(string $orderByAllowed): ProductDemand
    {
        $this->orderByAllowed = $orderByAllowed;
        return $this;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return ProductDemand
     */
    public function setCategories(array $categories): ProductDemand
    {
        $this->categories = $categories;
        return $this;
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
     * @return ProductDemand
     */
    public function setCategoryConjunction(string $categoryConjunction): ProductDemand
    {
        $this->categoryConjunction = $categoryConjunction;
        return $this;
    }
}
