<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\Domain\Repository;

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
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
 * Class OrderRepository
 * @package Pixelant\PxaProductManager\Domain\Repository
 */
class OrderRepository extends Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'crdate' => QueryInterface::ORDER_DESCENDING
    );

    /**
     * Find all order in current root line
     * @param int $pid
     * @return QueryResultInterface
     */
    public function findAllInRootLine(int $pid): QueryResultInterface
    {
        $queryGenerator = $this->objectManager->get(QueryGenerator::class);
        $storage = $queryGenerator->getTreeList($pid, 99, 0, 1);

        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setStoragePageIds(GeneralUtility::intExplode(',', $storage))
            ->setRespectSysLanguage(false);

        return $query->execute();
    }
}
