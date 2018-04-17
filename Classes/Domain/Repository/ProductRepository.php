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
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ProductRepository extends AbstractDemandRepository
{
    /**
     * @var \Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository
     * @inject
     */
    protected $attributeValueRepository;

    /**
     * Find all product with storage or all
     *
     * @param bool $respectStorage
     * @return QueryResultInterface
     */
    public function findAll($respectStorage = true)
    {
        $query = $this->createQuery();

        if (!$respectStorage) {
            $query->getQuerySettings()->setRespectStoragePage(false);
        }

        return $query->execute();
    }

    /**
     * Find products by categories
     *
     * @param array $categories
     * @param bool $showHidden
     * @param array $orderings
     * @param string $conjunction
     * @return array|QueryResultInterface
     */
    public function findProductsByCategories(
        array $categories,
        bool $showHidden = false,
        array $orderings = ['name' => QueryInterface::ORDER_DESCENDING],
        string $conjunction = 'and',
        int $limit = 0
    ) {
        if (empty($categories)) {
            return [];
        }

        // Find products our own way, because CategoryCollection::load doesn't have options to set the ordering
        $query = $this->createQuery();
        if (true === $showHidden) {
            $query
                ->getQuerySettings()
                ->setIgnoreEnableFields(true)
                ->setEnableFieldsToBeIgnored(['disabled']);
        }

        $constraints = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        $query->matching(
            $this->createConstraintFromConstraintsArray(
                $query,
                $constraints,
                $conjunction
            )
        );

        $query->setOrderings($orderings);

        // Set limit
        if ($limit > 0) {
            $query->setLimit($limit);
        }

        return $query->execute();
    }

    /**
     * Count products for category
     *
     * @param Category $category
     * @return int
     */
    public function countByCategory(Category $category): int
    {
        $query = $this->createQuery();

        $query->matching(
            $query->contains('categories', $category)
        );

        return $query->count();
    }

    /**
     * findProductsByUIds
     *
     * @param array $uids
     * @return QueryResultInterface|array
     */
    public function findProductsByUids(array $uids = [])
    {
        if (empty($uids)) {
            return [];
        }

        $query = $this->createQuery();

        // Disable language and storage check, because we are using uids
        $query
            ->getQuerySettings()
            ->setRespectSysLanguage(false)
            ->setRespectStoragePage(false);

        $query->matching(
            $query->in('uid', $uids)
        );

        return $query->execute();
    }

    /**
     * Create constraints for all demand options
     *
     * @param QueryInterface $query
     * @param Demand $demand
     */
    protected function createConstraints(QueryInterface $query, Demand $demand)
    {
        $constraints = [];

        if (!empty($demand->getCategories())) {
            $constraints['categories'] = $this->createCategoryConstraints(
                $query,
                $demand->getCategories(),
                $demand->getCategoryConjunction()
            );
        }

        if (!empty($demand->getFilters())) {
            $filterConstraints = $this->createFilteringConstraints(
                $query,
                $demand->getFilters(),
                $demand->getFiltersConjunction()
            );
            if ($filterConstraints !== false) {
                $constraints['filters'] = $filterConstraints;
            }
        }

        if (!empty($constraints)) {
            $query->matching(
                $this->createConstraintFromConstraintsArray(
                    $query,
                    $constraints,
                    'and'
                )
            );
        }
    }

    /**
     * Filters
     * Filters are generated on FE in ProductManager.Filtering.js
     * Array
     * (
     *  [2-13] => Array // type + uid of filter
     * (
     *  [type] => 2 // type of filter
     *  [attributeUid] => 13 // UID of attribute or parent category
     *  [value] => Array // array of values
     * (
     *      [0] => 3
     *  )
     * )
     * @param QueryInterface $query
     * @param array $filters
     * @param string $conjunction
     * @return mixed
     */
    protected function createFilteringConstraints(QueryInterface $query, array $filters, string $conjunction = 'or')
    {
        $constraints = [];
        $ranges = [];

        foreach ($filters as $filter) {
            if (!empty($filter['value'])) {
                switch ((int)$filter['type']) {
                    case Filter::TYPE_ATTRIBUTES:
                        $filterConstraints = [];

                        foreach ($filter['value'] as $value) {
                            $attributeValues = $this->attributeValueRepository->findAttributeValuesByAttributeAndValue(
                                (int)$filter['attributeUid'],
                                $value,
                                true
                            );
                            if (empty($attributeValues)) {
                                // force no result for filter constraint if no value was found but filter was set on FE
                                $filterConstraints[] = $query->contains('attributeValues', 0);
                            } else {
                                foreach ($attributeValues as $attributeValue) {
                                    $filterConstraints[] = $query->contains('attributeValues', $attributeValue['uid']);
                                }
                            }
                        }
                        if (!empty($filterConstraints)) {
                            $constraints[] = $this->createConstraintFromConstraintsArray(
                                $query,
                                $filterConstraints,
                                'or'
                            );
                        }
                        break;
                    case Filter::TYPE_CATEGORIES:
                        $categoriesConstraints = [];
                        foreach ($filter['value'] as $value) {
                            $categoriesConstraints[] = $query->contains('categories', $value);
                        }

                        $constraints[] = $this->createConstraintFromConstraintsArray(
                            $query,
                            $categoriesConstraints,
                            'or'
                        );
                        break;
                    case Filter::TYPE_ATTRIBUTES_MINMAX:
                        // need to just prebuild array since minmax attribute filter can consist of two inputs
                        list($value, $rangeType) = $filter['value'];
                        $rangeKey = (int)$filter['attributeUid'];

                        $ranges[$rangeKey][$rangeType] = $value;

                        break;
                    default:
                        // only two are supported for now
                }
            }
        }

        // go through ranges after all filters have been processed
        // since they can have value from two filter inputs
        if (!empty($ranges)) {
            foreach ($ranges as $attributeId => $range) {
                $rangeConstraints = [];

                $attributeValues = $this->attributeValueRepository->findAttributeValuesByAttributeAndMinMaxOptionValues(
                    (int)$attributeId,
                    isset($range['min']) ? (int)$range['min'] : null,
                    isset($range['max']) ? (int)$range['max'] : null
                );

                if (empty($attributeValues)) {
                    // force no result for filter constraint if no value was found but filter was set on FE
                    $rangeConstraints[] = $query->contains('attributeValues', 0);
                } else {
                    foreach ($attributeValues as $attributeValue) {
                        $rangeConstraints[] = $query->contains('attributeValues', $attributeValue['uid']);
                    }
                }

                if (!empty($rangeConstraints)) {
                    $constraints[] = $this->createConstraintFromConstraintsArray(
                        $query,
                        $rangeConstraints,
                        'or'
                    );
                }
            }
        }

        if (!empty($constraints)) {
            return $this->createConstraintFromConstraintsArray(
                $query,
                $constraints,
                strtolower($conjunction)
            );
        }

        return false;
    }

    /**
     * Create categories constraints
     *
     * @param QueryInterface $query
     * @param array $categories
     * @param string $conjunction
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface
     */
    protected function createCategoryConstraints(QueryInterface $query, array $categories, string $conjunction = 'or')
    {
        $constraints = [];

        foreach ($categories as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        return $this->createConstraintFromConstraintsArray(
            $query,
            $constraints,
            strtolower($conjunction)
        );
    }
}
