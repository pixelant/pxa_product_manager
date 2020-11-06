<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

/*
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
 */

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Event\Repository\FilterConstraints;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ProductRepository extends AbstractDemandRepository
{
    use AbleFindByUidList;

    /**
     * @param QueryInterface $query
     * @param ProductDemand|DemandInterface $demand
     * @return array
     */
    protected function createConstraints(QueryInterface $query, DemandInterface $demand): array
    {
        $constraints = [];

        // In case demand has category filter, skip setting categories constraint,
        // since filter already has it's own categories restriction
        if (!empty($demand->getCategories()) && !$demand->hasFiltersCategoryFilter()) {
            $constraints['categories'] = $this->categoriesConstraint($query, $demand);
        }

        // If filters are present in demand it mean this is lazy loading request.
        // Categories will be set as filter constraint.
        // Attributes and categories are sharing same conjunction filters settings.
        if (!empty($demand->getFilters())) {
            $constraints['filters'] = $this->filtersConstraint($query, $demand);
        }

        return $constraints;
    }

    /**
     * Create categories constraint from demand.
     *
     * @param QueryInterface $query
     * @param DemandInterface|ProductDemand $demand
     * @return ConstraintInterface
     */
    protected function categoriesConstraint(QueryInterface $query, DemandInterface $demand): ConstraintInterface
    {
        // If OR, just use in query, reduce number of joins
        // Or is always used for entry point demand of list/lazy loading
        if ($this->isOrConjunction($demand->getCategoryConjunction())) {
            return $query->in('categories.uid', $demand->getCategories());
        }

        return $this->createConstraintFromConstraintsArray(
            $query,
            $this->categoriesContainsConstraints($query, $demand->getCategories()),
            $demand->getCategoryConjunction()
        );
    }

    /**
     * Create categories constraints array.
     *
     * @param QueryInterface $query
     * @param array $categories
     * @return array
     */
    protected function categoriesContainsConstraints(QueryInterface $query, array $categories): array
    {
        $constraints = [];
        foreach ($categories as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        return $constraints;
    }

    /**
     * Create filters constraint.
     *
     * @param QueryInterface $query
     * @param ProductDemand|DemandInterface $demand
     * @return ConstraintInterface
     */
    protected function filtersConstraint(QueryInterface $query, DemandInterface $demand): ConstraintInterface
    {
        $constraints = [];

        foreach ($demand->getFilters() as $filterData) {
            $type = (int)$filterData['type'];
            $conjunction = $filterData['conjunction'];
            $value = $filterData['value'];

            if ($type === Filter::TYPE_CATEGORIES) {
                $constraints[] = $this->createConstraintFromConstraintsArray(
                    $query,
                    $this->categoriesContainsConstraints($query, $value),
                    $conjunction
                );
            } elseif ($type === Filter::TYPE_ATTRIBUTES) {
                $constraints[] = $this->attributeFilterConstraint(
                    $query,
                    (int)$filterData['attribute'],
                    $value,
                    $conjunction
                );
            }
        }

        $event = GeneralUtility::makeInstance(FilterConstraints::class, $demand, $query, $constraints);
        $this->dispatcher->dispatch(__CLASS__, 'filtersConstraintArray', [$event]);

        return $this->createConstraintFromConstraintsArray(
            $query,
            $event->getConstraints(),
            $demand->getFilterConjunction()
        );
    }

    /**
     * Create single filter attribute constraint.
     *
     * @param QueryInterface $query
     * @param int $attribute
     * @param array $values
     * @param string $conjunction
     * @return ConstraintInterface
     */
    protected function attributeFilterConstraint(
        QueryInterface $query,
        int $attribute,
        array $values,
        string $conjunction
    ): ConstraintInterface {
        // Create like constraint for each filter value
        $valueConstraints = array_map(function ($value) use ($query) {
            return $query->like('attributesValues.value', sprintf('%%,%s,%%', $value));
        }, $values);

        // Add attribute uid constraint to values constraints
        return $query->logicalAnd([
            $query->equals('attributesValues.attribute', $attribute),
            $this->createConstraintFromConstraintsArray($query, $valueConstraints, $conjunction),
        ]);
    }
}
