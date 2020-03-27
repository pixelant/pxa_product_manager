<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

/**
 * @package Pixelant\PxaProductManager\Domain\Resource
 */
class Filter extends AbstractResource
{
    /**
     * @return array
     */
    protected function extractableProperties(): array
    {
        return [
            'uid',
            'type',
            'options',
            'attributeUid',
            'conjunction',
        ];
    }
}
