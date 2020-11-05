<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ConfigurationProvider;

/**
 * Simple label.
 */
class LabelProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function overrideWithSpecificTca(array $tca): array
    {
        if ($this->isRequired()) {
            $tca['config']['eval']
                = $tca['config']['eval'] ? $tca['config']['eval'] . ',required' : 'required';
        }

        return $tca;
    }
}
