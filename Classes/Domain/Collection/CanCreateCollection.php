<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Collection;

use TYPO3\CMS\Core\Utility\GeneralUtility;

trait CanCreateCollection
{
    /**
     * Able to create collections.
     *
     * @param $items
     * @return Collection
     */
    protected function collection($items): Collection
    {
        return GeneralUtility::makeInstance(Collection::class, $items);
    }
}
