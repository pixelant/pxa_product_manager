<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Provider\AttributesConfiguration;

/**
 * Checkbox configuration
 */
class CheckboxProvider extends AbstractProvider
{
    /**
     * @inheritDoc
     */
    protected function overrideWithSpecificTca(array $tca): array
    {
        return $tca;
    }
}
