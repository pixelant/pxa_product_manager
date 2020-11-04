<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ConfigurationProvider;

/**
 * Checkbox configuration.
 */
class CheckboxProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function overrideWithSpecificTca(array $tca): array
    {
        return $tca;
    }
}
