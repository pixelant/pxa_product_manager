<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ConfigurationProvider;

interface ProviderInterface
{
    /**
     * Return TCA configuration of attribute.
     *
     * @return array
     */
    public function get(): array;
}
