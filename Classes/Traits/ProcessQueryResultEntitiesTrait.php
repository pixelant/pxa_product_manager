<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Traits;

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Sorting query results trait
 *
 * @package Pixelant\PxaProductManager\Traits
 */
trait ProcessQueryResultEntitiesTrait
{
    /**
     * Sort entities according to uid list order
     *
     * @param QueryResultInterface|array $originalEntities
     * @param array $customSorting
     * @param string $property
     * @param bool $orderDescending
     * @return QueryResultInterface|array
     */
    protected function sortEntitiesAccordingToList(
        $originalEntities,
        array $customSorting,
        string $property = 'uid',
        bool $orderDescending = false
    ) {
        // Check if it's array or query result
        if (is_object($originalEntities) && $originalEntities instanceof QueryResultInterface) {
            $entities = $originalEntities->toArray();
            $isQueryResult = true;
        } else {
            $entities = $originalEntities;
            $isQueryResult = false;
            unset($originalEntities);
        }

        $sorted = [];

        foreach ($entities as $entity) {
            if (is_object($entity) && $entity instanceof AbstractDomainObject) {
                $propertyValue = ObjectAccess::getProperty($entity, $property);
            } elseif (is_array($entity) && isset($entity[$property])) {
                $propertyValue = $entity[$property];
            } else {
                continue;
            }

            $ak = array_keys($customSorting, $propertyValue);
            foreach ($ak as $idx) {
                $sorted[$idx] = $entity;
            }
        }

        if ($orderDescending) {
            krsort($sorted, SORT_NUMERIC);
        } else {
            ksort($sorted, SORT_NUMERIC);
        }

        // If was query result given regenerate it
        if ($isQueryResult) {
            // Clean query result
            foreach ($entities as $offset => $_) {
                $originalEntities->offsetUnset($offset);
            }

            foreach ($sorted as $offset => $entity) {
                $originalEntities->offsetSet($offset, $entity);
            }

            return $originalEntities;
        }

        return $sorted;
    }

    /**
     * Remove duplicated entries
     *
     * @param QueryResultInterface|array $queryResults
     * @return mixed
     */
    protected function removeDuplicatedEntries($queryResults)
    {
        // For query result
        $recordsFound = [];
        if ($queryResults instanceof QueryResult) {
            /**
             * @var int $offset
             * @var  AbstractDomainObject $queryResult
             */
            foreach ($queryResults->toArray() as $offset => $queryResult) {
                if (!in_array($queryResult->getUid(), $recordsFound)) {
                    $recordsFound[] = $queryResult->getUid();
                } else {
                    $queryResults->offsetUnset($offset);
                }
            }
        }

        // For raw results
        if (is_array($queryResults)) {
            foreach ($queryResults as $key => $queryResult) {
                $uid = $queryResults['uid'];
                if (!in_array($uid, $recordsFound)) {
                    $recordsFound[] = $uid;
                } else {
                    unset($queryResults[$key]);
                }
            }
        }

        return $queryResults;
    }
}
