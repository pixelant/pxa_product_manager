<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ConfigurationProvider;

/**
 * Link TCA.
 */
class LinkProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function overrideWithSpecificTca(array $tca): array
    {
        if ($this->isRequired()) {
            $tca['config']['eval'] = 'required';
        }

        return $tca;
    }
}
