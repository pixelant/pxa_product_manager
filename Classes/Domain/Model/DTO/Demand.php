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
    protected ?string $orderBy = null;

    /**
     * @var string
     */
    protected ?string $orderDirection = null;

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
     * @return Demand
     */
    public function setStoragePid(?array $storagePid): Demand
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
     * @return Demand
     */
    public function setLimit(int $limit): Demand
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
     * @return Demand
     */
    public function setOffSet(int $offSet): Demand
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
     * @return Demand
     */
    public function setOrderBy(string $orderBy): Demand
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
     * @return Demand
     */
    public function setOrderDirection(string $orderDirection): Demand
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
     * @return Demand
     */
    public function setOrderByAllowed(string $orderByAllowed): Demand
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
     * @return Demand
     */
    public function setCategories(array $categories): Demand
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
     * @return Demand
     */
    public function setCategoryConjunction(string $categoryConjunction): Demand
    {
        $this->categoryConjunction = $categoryConjunction;
        return $this;
    }
}
