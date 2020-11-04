<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;

/**
 * Interface DemandRepositoryInterface.
 */
interface DemandRepositoryInterface
{
    public function findDemanded(DemandInterface $demand);
}
