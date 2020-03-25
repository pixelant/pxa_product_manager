<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

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

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ProductRepository extends AbstractDemandRepository
{
    use AbleFindByUidList;

    /**
     * @param FilterRepository $filterRepository
     */
    public function injectFilterRepository(FilterRepository $filterRepository)
    {
        $this->filterRepository = $filterRepository;
    }

    /**
     * @param QueryInterface $query
     * @param ProductDemand|DemandInterface $demand
     * @return array
     */
    protected function createConstraints(QueryInterface $query, DemandInterface $demand): array
    {
        $constraints = [];
        if (! empty($demand->getCategories())) {
            $constraints['categories'] = $this->categoriesConstraint($query, $demand);
        }

        if (! empty($demand->getAttributes())) {
            $constraints['attributes'] = $this->filtersConstraint($query, $demand);
        }

        return $constraints;
    }

    /**
     * Create categories constraint
     *
     * @param QueryInterface $query
     * @param DemandInterface|ProductDemand $demand
     * @return ConstraintInterface
     */
    protected function categoriesConstraint(QueryInterface $query, DemandInterface $demand): ConstraintInterface
    {
        $constraints = [];
        foreach ($demand->getCategories() as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        return $this->createConstraintFromConstraintsArray($query, $constraints, $demand->getCategoryConjunction());
    }

    /**
     * Create filters constraint
     *
     * @param QueryInterface $query
     * @param ProductDemand|DemandInterface $demand
     */
    protected function filtersConstraint(QueryInterface $query, DemandInterface $demand)
    {
        $constraints = [];
        foreach ($demand->getAttributes() as $attributeUid => $filterData) {
            $constraints[] = $query->logicalAnd([
                $query->equals('attributesValues.attribute', $attributeUid),
                $query->equals('attributesValues.value', $filterData['value'][0])
            ]);
        }

        return $this->createConstraintFromConstraintsArray($query, $constraints, $demand->getFilterConjunction());
    }
}
