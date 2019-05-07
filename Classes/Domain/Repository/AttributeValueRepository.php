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

use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

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
     * @param array $values
     * @param bool $rawResult
     * @return QueryResultInterface|array
     */
    public function findAttributeValuesByAttributeAndValues($attribute, array $values, string $filterConjunction = 'or', $rawResult = false)
    {
        $query = $this->createQuery();

        $query
            ->getQuerySettings()
            ->setRespectStoragePage(false);

        $valuesConstraints = [];
        foreach ($values as $value) {
            $valuesConstraints[] = $this->createInRowQuery($query, 'value', $value);
        }

        $query->matching(
            $query->logicalAnd([
                $query->equals('attribute', $attribute),
                $filterConjunction === 'and'
                    ? $query->logicalAnd($valuesConstraints)
                    : $query->logicalOr($valuesConstraints)
            ])
        );

        return $query->execute($rawResult);
    }

    /**
     * Find attribute values by their attribute and option values (higher or lower)
     *
     * @param $attribute
     * @param int $minValue
     * @param int $maxValue
     * @return QueryResultInterface|array
     */
    public function findAttributeValuesByAttributeAndMinMaxOptionValues(
        int $attribute,
        int $minValue = null,
        int $maxValue = null
    ) {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_pxaproductmanager_domain_model_attributevalue'
        );

        $constraints[] = $queryBuilder->expr()->eq(
            'tx_pxaproductmanager_domain_model_attributevalue.attribute',
            $queryBuilder->createNamedParameter($attribute, \PDO::PARAM_INT)
        );

        if ($minValue !== null) {
            $constraints[] = $queryBuilder->expr()->gte(
                'option.value',
                $queryBuilder->createNamedParameter($minValue, \PDO::PARAM_INT)
            );
        }

        if ($maxValue !== null) {
            $constraints[] = $queryBuilder->expr()->lte(
                'option.value',
                $queryBuilder->createNamedParameter($maxValue, \PDO::PARAM_INT)
            );
        }

        $result = $queryBuilder
            ->select('tx_pxaproductmanager_domain_model_attributevalue.uid')
            ->from('tx_pxaproductmanager_domain_model_attributevalue')
            ->join(
                'tx_pxaproductmanager_domain_model_attributevalue',
                'tx_pxaproductmanager_domain_model_option',
                'option',
                // ugly fix for "IN". but using just "IN" doesn't work
                /**
                 * @TODO find nice way for this query
                 */
                sprintf(
                    'CONCAT(\',\', %s, \',\') LIKE CONCAT(\'%%,\', %s, \',%%\')',
                    $queryBuilder->quoteIdentifier('tx_pxaproductmanager_domain_model_attributevalue.value'),
                    $queryBuilder->quoteIdentifier('option.uid')
                )
            )
            ->where(...$constraints)
            ->groupBy('tx_pxaproductmanager_domain_model_attributevalue.uid')
            ->execute()
            ->fetchAll();

        return $result;
    }
}
