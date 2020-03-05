<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Arrayable;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\Factory\CategoryDemandFactory;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;

/**
 * Generate categories navigation tree
 *
 * @package Pixelant\PxaProductManager\Service
 */
class NavigationService
{
    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * @var CategoryDemandFactory
     */
    protected CategoryDemandFactory $demandFactory;

    /**
     * @var Category|null
     */
    protected ?Category $activeCategory;

    /**
     * @var int
     */
    protected int $rootCategoryUid;

    /**
     * @var bool
     */
    protected bool $hideCategoriesWithoutProducts = false;

    /**
     * @var bool
     */
    protected bool $expandAll = false;

    /**
     * @param CategoryDemandFactory $categoryDemandFactory
     */
    public function injectCategoryDemandFactory(CategoryDemandFactory $categoryDemandFactory)
    {
        $this->demandFactory = $categoryDemandFactory;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Build navigation items
     *
     * @param Category|null $activeCategory
     * @param array $settings
     */
    public function build(?Category $activeCategory, array $settings)
    {
        $this->activeCategory = $activeCategory;
        $this->parseSettings($settings);
    }

    /**
     * Read plugin settings
     *
     * @param array $settings
     */
    protected function parseSettings(array $settings): void
    {
        $this->rootCategoryUid = (int)$settings['list']['entryNavigationCategory'];
        $this->hideCategoriesWithoutProducts = (bool)$settings['navigation']['hideCategoriesWithoutProducts'];
        $this->expandAll = (bool)$settings['navigation']['expandAll'];
    }
}
