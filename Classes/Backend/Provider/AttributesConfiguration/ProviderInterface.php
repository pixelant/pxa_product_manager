<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Provider\AttributesConfiguration;

/**
 * @package Pixelant\PxaProductManager\Configuration\AttributesTCA
 */
interface ProviderInterface
{
    /**
     * Return TCA configuration of attribute
     *
     * @return array
     */
    public function get(): array;
}
