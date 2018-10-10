<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO;

/**
 * Interface DemandInterface
 * @package Pixelant\PxaProductManager\Domain\Model\DTO
 */
interface DemandInterface
{
    /**
     * Query limit
     *
     * @return int
     */
    public function getLimit(): int;

    /**
     * Query offset
     *
     * @return int
     */
    public function getOffSet(): int;

    /**
     * Query order field
     *
     * @return string
     */
    public function getOrderBy(): string ;

    /**
     * Query order direction
     *
     * @return string
     */
    public function getOrderDirection(): string;

    /**
     * List of allowed order by fields
     *
     * @return string
     */
    public function getOrderByAllowed(): string;

    /**
     * Array of storage pids
     *
     * @return array
     */
    public function getStoragePid(): array;
}
