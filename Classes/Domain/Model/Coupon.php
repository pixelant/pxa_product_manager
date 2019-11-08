<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
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

/**
 * A coupon modifying the price of an order
 *
 * Class Coupon
 * @package Pixelant\PxaProductManager\Domain\Model
 */
class Coupon extends AbstractEntity
{
    const TYPE_CASH_REBATE = 0;
    const TYPE_PERCENTAGE_REBATE = 1;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $code
     */
    protected $code;

    /**
     * @var int $usageLimit
     */
    protected $usageLimit = 0;

    /**
     * @var float
     */
    protected $costLimit = 0.0;

    /**
     * @var int $usageCount
     */
    protected $usageCount = 0;

    /**
     * @var float $totalCost
     */
    protected $totalCost = 0.0;

    /**
     * @var float $value
     */
    protected $value = 0.0;

    /**
     * @var int $type
     */
    protected $type;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setName(string $name)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getUsageLimit(): int
    {
        return $this->maxUses;
    }

    /**
     * @param int $maxUses
     */
    public function setUsageLimit(int $usageLimit)
    {
        $this->maxUses = $maxUses;
    }

    /**
     * @return float
     */
    public function getCostLimit(): float
    {
        return $this->maxCost;
    }

    /**
     * @param float $maxCost
     */
    public function setCostLimit(float $costLimit)
    {
        $this->maxCost = $maxCost;
    }

    /**
     * @return int
     */
    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    /**
     * @param int $usageCount
     */
    public function setUsageCount(int $usageCount)
    {
        $this->usageCount = $usageCount;
    }

    /**
     * @return float
     */
    public function getTotalCost(): float
    {
        return $this->totalCost;
    }

    /**
     * @param float $totalCost
     */
    public function setTotalCost(float $totalCost)
    {
        $this->totalCost = $totalCost;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

}
