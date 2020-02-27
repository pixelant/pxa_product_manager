<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model;


use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Use in models that can cache their properties on heave calculations/operations
 * For example categories root line should be fetched only once
 *
 * @package Pixelant\PxaProductManager\Domain\Model
 */
trait AbleCacheProperties
{
    /**
     * Cached properties
     *
     * @var array
     */
    protected array $cacheProperties = [];

    /**
     * Get cached property or init it and save in cache
     * @param string $key Property or getter method name
     * @param callable $closure Should do the logic of property initialization and return result
     * @return mixed
     */
    protected function getCachedProperty(string $key, callable $closure)
    {
        $key = $this->cachePropertyKeyToProperty($key);
        if (array_key_exists($key, $this->cacheProperties)) {
            return $this->cacheProperties[$key];
        }

        $this->cacheProperties[$key] = $closure();
        return $this->cacheProperties[$key];
    }

    /**
     * Convert getter method to property or assume given key is property name
     *
     * @param string $key
     * @return string
     */
    protected function cachePropertyKeyToProperty(string $key): string
    {
        if (strpos($key, 'get') === 0) {
            return lcfirst(substr($key, 3));
        }

        return $key;
    }
}
