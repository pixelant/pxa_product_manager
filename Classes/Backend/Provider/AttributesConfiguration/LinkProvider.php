<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Provider\AttributesConfiguration;

/**
 * Link TCA
 */
class LinkProvider extends AbstractProvider
{
    /**
     * @inheritDoc
     */
    protected function overrideWithSpecificTca(array $tca): array
    {
        if ($this->isRequired()) {
            $tca['config']['eval'] = 'required';
        }

        return $tca;
    }
}
