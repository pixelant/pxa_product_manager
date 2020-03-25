<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

/**
 * @package Pixelant\PxaProductManager\Domain\Resource
 */
class Filter extends AbstractResource
{
    /**
     * @var array
     */
    protected array $extractableProperties = [
        'uid',
        'type',
        'options',
        'attributeUid',
        'conjunction',
    ];
}
