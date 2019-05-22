<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO;

/**
 * Class FiltersAvailableOptions
 * @package Pixelant\PxaProductManager\Domain\Model\DTO
 */
class FiltersAvailableOptions implements \JsonSerializable
{
    /**
     * Key that will match all filters
     */
    const ALL_FILTERS_KEY = 'all';

    /**
     * Keep available categories options for filter
     *
     * @var array
     */
    protected $availableCategories = [];

    /**
     * Keep available attributes values options
     *
     * @var array
     */
    protected $availableAttributes = [];

    /**
     * Set available filter categories for non-active filter
     *
     * @param array $categories
     */
    public function setAvailableCategoriesForAll(array $categories)
    {
        $this->availableCategories[self::ALL_FILTERS_KEY] = $categories;
    }

    /**
     * Set available filter categories for certain filter from demand
     *
     * @param int $filterUid
     * @param array $categories
     */
    public function setAvailableCategoriesForFilter(int $filterUid, array $categories)
    {
        $this->availableCategories[$filterUid] = $categories;
    }

    /**
     * Set available categories
     *
     * @param array $availableCategories
     */
    public function setAvailableCategories(array $availableCategories)
    {
        $this->availableCategories = $availableCategories;
    }

    /**
     * @return array
     */
    public function getAvailableCategories(): array
    {
        return $this->availableCategories;
    }

    /**
     * Set available attributes
     *
     * @param array $availableAttributes
     */
    public function setAvailableAttributes(array $availableAttributes)
    {
        $this->availableAttributes = $availableAttributes;
    }

    /**
     * Set available filter attribute options for non-active filter
     *
     * @param array $availableAttributes
     */
    public function setAvailableAttributesForAll(array $availableAttributes)
    {
        $this->availableAttributes[self::ALL_FILTERS_KEY] = $availableAttributes;
    }

    /**
     * Set available filter attribute options for filter from demand
     *
     * @param int $filterUid
     * @param array $availableAttributes
     */
    public function setAvailableAttributesForFilter(int $filterUid, array $availableAttributes)
    {
        $this->availableAttributes[$filterUid] = $availableAttributes;
    }

    /**
     * @return array
     */
    public function getAvailableAttributes(): array
    {
        return $this->availableAttributes;
    }

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'availableCategories' => $this->availableCategories,
            'availableAttributes' => $this->availableAttributes
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
