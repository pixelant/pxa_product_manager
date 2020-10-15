<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ConfigurationProvider;

/**
 * Simple input
 */
class DateTimeProvider extends InputProvider
{
    /**
     * @inheritDoc
     */
    protected function overrideWithSpecificTca(array $tca): array
    {
        if ($this->isRequired()) {
            $tca['config']['eval'] = $tca['config']['eval']
                ? $tca['config']['eval'] . ',required'
                : 'required';
        }

        return $tca;
    }
}
