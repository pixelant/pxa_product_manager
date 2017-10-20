<?php
namespace Pixelant\PxaProductManager\Domain\Repository;

use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;

/**
 * Interface DemandRepositoryInterface
 * @package Pixelant\PxaDealers\Domain\Repository
 */
interface DemandRepositoryInterface
{
    public function findDemanded(Demand $demand);
}
