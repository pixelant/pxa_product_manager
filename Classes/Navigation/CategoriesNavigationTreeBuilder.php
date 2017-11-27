<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Navigation;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Utility\CategoryUtility;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Build categories menu
 *
 * @package Pixelant\PxaProductManager\Navigation
 */
class CategoriesNavigationTreeBuilder
{
    /**
     * categoryRepository
     *
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Active categories
     *
     * @var array
     */
    protected $activeList = [];

    /**
     * Exclude from navigation
     *
     * @var array
     */
    protected $excludeCategories = [];

    /**
     * Expand all categories
     *
     * @var bool
     */
    protected $expandAll = false;

    /**
     * Default orderings
     *
     * @var array
     */
    protected $orderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING
    ];

    /**
     * CategoriesNavigationTreeBuilder constructor.
     */
    public function __construct()
    {
        $this->categoryRepository = MainUtility::getObjectManager()->get(CategoryRepository::class);
    }

    /**
     * Build menu tree
     *
     * @param int $rootCategory
     * @param int $activeCategory
     * @return array
     */
    public function buildTree(int $rootCategory, int $activeCategory = 0): array
    {
        /** @var Category $activeCategoryObject */
        $activeCategoryObject = $this->categoryRepository->findByUid($activeCategory);
        if ($activeCategoryObject !== null) {
            /** @var Category $category */
            foreach (CategoryUtility::getParentCategories($activeCategoryObject) as $category) {
                $this->activeList[] = $category->getUid();
            }
            // add current too
            $this->activeList[] = $activeCategory;
        }

        // Navigation tree
        /** @var Category $rootCategory */
        $rootCategory = $this->categoryRepository->findByUid($rootCategory);
        $treeData = [
            'rootCategory' => $rootCategory,
            'subItems' => [],
            'level' => 1
        ];
        $subItems = $this->findSubCategories($rootCategory);

        if ($subItems->count() > 0) {
            $this->buildDeepTree(
                $subItems,
                $activeCategory,
                $treeData['subItems'],
                2
            );
        }

        return $treeData;
    }

    /**
     * @return array
     */
    public function getExcludeCategories(): array
    {
        return $this->excludeCategories;
    }

    /**
     * @param array $excludeCategories
     * @return CategoriesNavigationTreeBuilder
     */
    public function setExcludeCategories(array $excludeCategories)
    {
        $this->excludeCategories = $excludeCategories;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExpandAll(): bool
    {
        return $this->expandAll;
    }

    /**
     * @param bool $expandAll
     * @return CategoriesNavigationTreeBuilder
     */
    public function setExpandAll(bool $expandAll)
    {
        $this->expandAll = $expandAll;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderings(): array
    {
        return $this->orderings;
    }

    /**
     * @param array $orderings
     * @return CategoriesNavigationTreeBuilder
     */
    public function setOrderings(array $orderings)
    {
        $this->orderings = $orderings;
        return $this;
    }

    /**
     * Build navigation tree
     *
     * @param QueryResultInterface $categories
     * @param int $activeCategoryUid
     * @param array $treeData
     * @param int $level
     */
    protected function buildDeepTree(
        QueryResultInterface $categories,
        int $activeCategoryUid,
        array &$treeData,
        int $level = 0
    ) {
        /** @var Category $category */
        foreach ($categories as $category) {
            if (!in_array($category->getUid(), $this->excludeCategories)) {
                $treeData[$category->getUid()] = [
                    'category' => $category,
                    'subItems' => [],
                    'isCurrent' => $category->getUid() === $activeCategoryUid,
                    'isActive' => in_array($category->getUid(), $this->activeList),
                    'level' => $level
                ];
                $subItems = $this->findSubCategories($category);

                if ($subItems->count() > 0
                    && ($this->expandAll || $treeData[$category->getUid()]['isActive'])
                ) {
                    $this->buildDeepTree(
                        $subItems,
                        $activeCategoryUid,
                        $treeData[$category->getUid()]['subItems'],
                        $level + 1
                    );
                }
            }
        }
    }

    /**
     * Get subcategories with order
     *
     * @param Category $parentCategory
     * @return QueryResultInterface
     */
    protected function findSubCategories(Category $parentCategory)
    {
        return $this->categoryRepository->findByParent(
            $parentCategory,
            $this->orderings
        );
    }
}
