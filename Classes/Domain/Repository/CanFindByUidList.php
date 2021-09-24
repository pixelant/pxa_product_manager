<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

trait CanFindByUidList
{
    /**
     * Find by uids list.
     *
     * @param array $uids
     * @return QueryResultInterface
     */
    public function findByUids(array $uids): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->in('uid', $uids)
        );

        return $query->execute();
    }

    /**
     * "Overlay" list of uids when language overlay mode is false.
     * Fetching records from repository by list of uids will be
     * empty without language overlay mode. This function can be
     * used to fetch corresponding uid list for current language.
     *
     * @param array $uids
     * @return array
     */
    public function overlayUidList(array $uids): array
    {
        $query = $this->createQuery();

        // If extbase already is in overlay mode, return original list of uids.
        $overlayMode = $query->getQuerySettings()->getLanguageOverlayMode();
        if ($overlayMode) {
            return $uids;
        }

        // No "overlay" needed for default language.
        $languageId = $query->getQuerySettings()->getLanguageUid() ?? 0;
        if ($languageId === 0) {
            return $uids;
        }

        // Return original list if table for query can't be determined.
        $table = $query->getSource()->getSelectorName();
        if (empty($table)) {
            return $uids;
        }

        // Return original list if language field ot translaition source field can't be determined.
        $languageField = $GLOBALS['TCA'][$table]['ctrl']['languageField'];
        $translationSource = $GLOBALS['TCA'][$table]['ctrl']['transOrigPointerField'];
        if (empty($languageField) || empty($translationSource)) {
            return $uids;
        }

        // Fetch uids of localized records.
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $records = $queryBuilder->select(...['uid', $translationSource])
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq(
                    $languageField,
                    $queryBuilder->createNamedParameter($languageId, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->in(
                    $translationSource,
                    $queryBuilder->createNamedParameter(
                        $uids,
                        \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->execute()
            ->fetchAllAssociative();

        if (count($records) > 0) {
            // Add sorting based on original list.
            foreach ($records as $index => $record) {
                $records[$index]['sorting'] = array_search((int)$record[$translationSource], $uids, true);
            }
            // Fix sorting according original uids.
            usort($records, function ($a, $b) {
                return $a['sorting'] > $b['sorting'];
            });

            $uids = array_column($records, 'uid');
        }

        return $uids;
    }
}
