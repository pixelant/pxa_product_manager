<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017
 *
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

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Filters
 */
class FilterRepository extends Repository
{
    /**
     * Uids array
     *
     * @param array $uids
     * @return array|QueryResultInterface
     */
    public function findByUidList(array $uids)
    {
        if (empty($uids)) {
            return [];
        }

        $query = $this->createQuery();
        // when looking by uid need to disable sys_language to find translated record.
        $query
            ->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setRespectSysLanguage(false);

        $query->matching(
            $query->in('uid', $uids)
        );

        return $query->execute();
    }
}
