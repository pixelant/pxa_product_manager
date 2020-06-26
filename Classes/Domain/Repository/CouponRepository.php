<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;


use Pixelant\PxaProductManager\Domain\Model\Coupon;
use TYPO3\CMS\Extbase\Persistence\Repository;

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
 * Handles order coupons such as discounts
 *
 * Class CouponRepository
 * @package Pixelant\PxaProductManager\Domain\Repository
 */
class CouponRepository extends Repository
{
    /**
     * Finds a coupon by code
     *
     * @param $code
     *
     * @return Coupon|null
     */
    public function findByCaseInsensitiveCode(string $code): ?Coupon
    {
        $query = $this->createQuery();

        /** @var Coupon|null $coupon */
        $coupon = $query
            ->matching($query->like('code', $code, false))
            ->execute()
            ->getFirst();

        return $coupon;
    }
}
