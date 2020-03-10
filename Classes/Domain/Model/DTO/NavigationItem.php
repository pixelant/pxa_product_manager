<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO;

use Pixelant\PxaProductManager\Domain\Model\Category;

/**
 * @package Pixelant\PxaProductManager\Domain\Model\DTO
 */
class NavigationItem
{
    /**
     * @var Category
     */
    protected Category $category;

    /**
     * @var NavigationItem[]
     */
    protected array $subItems = [];

    /**
     * @var bool
     */
    protected bool $isActive;

    /**
     * @var bool
     */
    protected bool $isCurrent;

    /**
     * @param Category $category
     * @param bool $isActive
     * @param bool $isCurrent
     */
    public function __construct(Category $category, bool $isActive, bool $isCurrent)
    {
        $this->category = $category;
        $this->isActive = $isActive;
        $this->isCurrent = $isCurrent;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return NavigationItem
     */
    public function setCategory(Category $category): NavigationItem
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return NavigationItem[]
     */
    public function getSubItems(): array
    {
        return $this->subItems;
    }

    /**
     * @param NavigationItem[] $subItems
     * @return NavigationItem
     */
    public function setSubItems(array $subItems): NavigationItem
    {
        $this->subItems = $subItems;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return NavigationItem
     */
    public function setIsActive(bool $isActive): NavigationItem
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }

    /**
     * @param bool $isCurrent
     * @return NavigationItem
     */
    public function setIsCurrent(bool $isCurrent): NavigationItem
    {
        $this->isCurrent = $isCurrent;
        return $this;
    }
}
