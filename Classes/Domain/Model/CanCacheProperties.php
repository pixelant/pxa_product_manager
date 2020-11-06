<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model;

/**
 * Use in models that can cache their properties on heave calculations/operations
 * For example categories root line should be fetched only once.
 */
trait CanCacheProperties
{
    /**
     * Cached properties.
     *
     * @var array
     */
    protected array $cacheProperties = [];

    /**
     * Get cached property or init it and save in cache.
     * @param string $key Property or getter method name
     * @param callable $closure Should do the logic of property initialization and return result
     * @return mixed
     */
    protected function getCachedProperty(string $key, callable $closure)
    {
        if (array_key_exists($key, $this->cacheProperties)) {
            return $this->cacheProperties[$key];
        }

        $this->cacheProperties[$key] = $closure();

        return $this->cacheProperties[$key];
    }
}
