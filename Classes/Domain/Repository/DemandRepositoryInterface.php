<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * Interface DemandRepositoryInterface.
 */
interface DemandRepositoryInterface
{
    public function findDemanded(DemandInterface $demand);

    public function createDemandQueryBuilder(DemandInterface $demand): QueryBuilder;

    public function getObjectClassName(): string;
}
