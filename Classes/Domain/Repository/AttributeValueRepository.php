<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

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
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AttributeValueRepository
 * @package Pixelant\PxaProductManager\Domain\Repository
 */
class AttributeValueRepository extends Repository
{
    /**
     * Help to find in row with list of values (2,3,4) and value 3
     *
     * @param QueryInterface $query
     * @param $field
     * @param $value
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\Qom\OrInterface
     */
    protected function createInRowQuery(QueryInterface $query, $field, $value)
    {
        return $query->logicalOr([
            $query->like($field, $value . ',%'),
            $query->like($field, '%,' . $value . ',%'),
            $query->like($field, '%,' . $value),
            $query->equals($field, $value)
        ]);
    }

    /**
     * Find attribute value by attribute and its value
     *
     * @param $attribute
     * @param $value
     * @param bool $rawResult
     * @return QueryResultInterface|array
     */
    public function findAttributeValuesByAttributeAndValue($attribute, $value, $rawResult = false)
    {
        $query = $this->createQuery();

        $query
            ->getQuerySettings()
            ->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd([
                $query->equals('attribute', $attribute),
                $this->createInRowQuery($query, 'value', $value)
            ])
        )->execute($rawResult);
    }
}
