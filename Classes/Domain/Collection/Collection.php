<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Collection;

use InvalidArgumentException;
use Pixelant\PxaProductManager\Arrayable;
use Traversable;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        $this->collection = $this->iterableToArray($collection);
    }

    /**
     * Sort collection by given order list by property
     *
     * @param $list
     * @param string $property
     * @return Collection
     */
    public function sortByOrderList($list, string $property): Collection
    {
        if (is_string($list)) {
            $list = GeneralUtility::trimExplode(',', $list, true);
        }
        if (! is_array($list)) {
            throw new \InvalidArgumentException(
                sprintf('Expect list to be of type string or array, "%s" given', gettype($list)),
                1584623916077
            );
        }

        $sorted = [];
        foreach ($this->mapWithKeysOfProperty($property)->toArray() as $key => $value) {
            $ak = array_keys($list, $key);
            foreach ($ak as $idx) {
                $sorted[$idx] = $value;
            }
        }
        ksort($sorted, SORT_NUMERIC);

        return new static($sorted);
    }

    /**
     * Return array of collection where key is value of given property
     *
     * @param string $property Name of property
     * @param callable $callback Callback for keys
     * @return Collection
     */
    public function mapWithKeysOfProperty(string $property, callable $callback = null): Collection
    {
        $keys = $this->pluck($property)->toArray();

        if ($callback) {
            $keys = array_map($callback, $keys);
        }

        return new static(array_combine($keys, $this->collection));
    }


    /**
     * Get array of values of given key
     *
     * @param string $property
     * @param callable|null $callback
     * @return Collection
     */
    public function pluck(string $property, callable $callback = null): Collection
    {
        $keys = array_map(fn($item) => ObjectAccess::getProperty($item, $property), $this->collection);

        if ($callback) {
            $keys = array_map($callback, $keys);
        }

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
     * Return collection of unique items
     *
     * @return Collection
     */
    public function unique(): Collection
    {
        $unique = [];

        return new static(array_filter($this->collection, function ($item) use (&$unique) {
            if (! in_array($item, $unique, true)) {
                $unique[] = $item;
                return true;
            }

            return false;
        }));
    }

    /**
     * Perform search of item by property
     *
     * @param string $property
     * @param mixed $value
     * @param callable|null $callback Callback that will be applied to property value
     * @return Collection
     */
    public function searchByProperty(string $property, $value, callable $callback = null): Collection
    {
        $matchItems = [];
        foreach ($this->collection as $item) {
            $propertyValue = ObjectAccess::getProperty($item, $property);
            if ($callback) {
                $propertyValue = $callback($propertyValue);
            }

            if ($propertyValue === $value) {
                $matchItems[] = $item;
            }
        }

        return new static($matchItems);
    }

    /**
     * Search by property and return first item
     * @param string $property
     * @param $value
     * @param callable|null $callback
     * @return  mixed Item if found, otherwise null
     */
    public function searchOneByProperty(string $property, $value, callable $callback = null)
    {
        return $this->searchByProperty($property, $value, $callback)->first();
    }

    /**
     * Return first item from collection
     *
     * @return mixed
     */
    public function first()
    {
        if (empty($this->collection)) {
            return null;
        }

        reset($this->collection);
        return current($this->collection);
    }

    /**
     * Filter collection by given callback
     *
     * @param callable $callback
     * @return Collection
     */
    public function filter(callable $callback): Collection
    {
        return new static(array_filter($this->collection, $callback));
    }

    /**
     * Add item to the beginning of collection
     *
     * @param mixed ...$items
     * @return Collection
     */
    public function unshift(...$items): Collection
    {
        $collection = $this->collection;

        array_unshift($collection, ...$items);

        return new static($collection);
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
        if (! is_iterable($items) && ! ($items instanceof Arrayable)) {
            throw new InvalidArgumentException(
                sprintf('Collection accept only iterable argument as collection, but "%s" given', gettype($items)),
                1582719546879
            );
        }

        if ($items instanceof Traversable) {
            return iterator_to_array($items);
        } elseif ($items instanceof Arrayable) {
            return $items->toArray();
        }

        return $items;
    }
}
