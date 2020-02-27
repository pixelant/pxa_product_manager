<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Provider\AttributesConfiguration;

use Pixelant\PxaProductManager\Domain\Model\Option;

/**
 * Selectbox TCA
 */
class SelectBoxProvider extends AbstractProvider
{
    /**
     * @inheritDoc
     */
    protected function overrideWithSpecificTca(array $tca): array
    {
        if ($this->isRequired()) {
            $tca['config']['minitems'] = 1;
        }

        $options = [];
        /** @var Option $option */
        foreach ($this->attribute->getOptions() as $option) {
            $options[] = [$option->getValue(), $option->getUid()];
        }

        if (empty($options)) {
            $tca['label'] .= ' (This attribute has no options. Please configure the attribute and add some options to it.)';
        }

        $tca['config']['items'] = $options;

        return $tca;
    }
}
