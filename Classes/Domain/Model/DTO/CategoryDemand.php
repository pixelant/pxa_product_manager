<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO;

use Pixelant\PxaProductManager\Domain\Model\Category;

/**
 * @package Pixelant\PxaProductManager\Domain\Model\DTO
 */
class CategoryDemand extends AbstractDemand
{
    /**
     * @var Category|int
     */
    protected $parent = null;

    /**
     * Show only enabled in navigation
     *
     * @var bool
     */
    protected bool $onlyVisibleInNavigation = false;

    /**
     * Hide categories that doesn't have products
     *
     * @var bool
     */
    protected bool $hideCategoriesWithoutProducts = false;

    /**
     * @return Category|null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category|int $parent
     * @return CategoryDemand
     */
    public function setParent($parent): CategoryDemand
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOnlyVisibleInNavigation(): bool
    {
        return $this->onlyVisibleInNavigation;
    }

    /**
     * @param bool $onlyVisibleInNavigation
     * @return CategoryDemand
     */
    public function setOnlyVisibleInNavigation(bool $onlyVisibleInNavigation): CategoryDemand
    {
        $this->onlyVisibleInNavigation = $onlyVisibleInNavigation;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideCategoriesWithoutProducts(): bool
    {
        return $this->hideCategoriesWithoutProducts;
    }

    /**
     * @param bool $hideCategoriesWithoutProducts
     * @return CategoryDemand
     */
    public function setHideCategoriesWithoutProducts(bool $hideCategoriesWithoutProducts): CategoryDemand
    {
        $this->hideCategoriesWithoutProducts = $hideCategoriesWithoutProducts;
        return $this;
    }
}
