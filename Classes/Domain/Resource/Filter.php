<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

class Filter extends AbstractResource
{
    /**
     * @return array
     */
    protected function extractableProperties(): array
    {
        return [
            'uid',
            'name',
            'label',
            'type',
            'options',
            'attributeUid',
            'conjunction',
        ];
    }
}
