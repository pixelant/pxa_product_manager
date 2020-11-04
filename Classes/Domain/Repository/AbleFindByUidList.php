<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

trait AbleFindByUidList
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
}
