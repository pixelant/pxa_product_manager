<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Collection;

use Pixelant\PxaProductManager\Arrayable;
use Traversable;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * @package Pixelant\PxaProductManager\Domain\Collection
 */
class Collection implements Arrayable
{
    /**
     * @var array
     */
    protected array $collection;

    /**
     * @param array|Traversable $collection
     */
    public function __construct($collection)
    {
        if (!is_iterable($collection) && !($collection instanceof Arrayable)) {
            throw new \InvalidArgumentException(
                sprintf('Collection accept only iterable argument as collection, but "%s" given', gettype($collection)),
                1582719546879
            );
        }

        $this->collection = $this->iterableToArray($collection);
    }

    /**
     * Return array of collection where key is value of given property
     *
     * @param string $property
     * @return Collection
     */
    public function mapWithKeysOfProperty(string $property): Collection
    {
        $keys = $this->pluck($property)->toArray();

        return new static(array_combine($keys, $this->collection));
    }


    /**
     * Get value by given key
     *
     * @param string $property
     * @return Collection
     */
    public function pluck(string $property): Collection
    {
        $keys = array_map(fn($item) => ObjectAccess::getProperty($item, $property), $this->collection);

        return new static($keys);
    }

    /**
     * Union current collection with items, but take only ones that doesn't exist with same property value
     *
     * @param $items
     * @param string $property
     * @return Collection
     */
    public function unionUniqueProperty($items, string $property): Collection
    {
        $unionItems = $this->mapWithKeysOfProperty($property)->toArray()
            + (new static($items))->mapWithKeysOfProperty($property)->toArray();

        return new static($unionItems);
    }

    /**
     * Remove first level of array and merge sub-items
     * All sub-items should be iterable
     *
     * @return $this
     */
    public function shiftLevel(): Collection
    {
        $items = array_merge(...array_values(array_map(
            fn($item) => $this->iterableToArray($item),
            $this->collection
        )));

        return new static($items);
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * Convert items to array
     *
     * @param $items
     * @return array
     */
    protected function iterableToArray($items): array
    {
        if ($items instanceof Traversable) {
            return iterator_to_array($items);
        } elseif ($items instanceof Arrayable) {
            return $items->toArray();
        }

        return $items;
    }
}
