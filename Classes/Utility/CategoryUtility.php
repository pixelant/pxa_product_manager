<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Pavlo Zaporozkyi <pavlo@pixelant.se>, Pixelant
 *
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
class CategoryUtility
{
    /**
     * Generate array of categories
     * @param $idList
     * @param bool $removeGivenIdListFromResult
     * @return array|mixed
     */
    public static function getCategoriesRootLine(array $idList, bool $removeGivenIdListFromResult = false): array
    {
        if (empty($idList)) {
            return [];
        }

        $cache = MainUtility::getCacheManager()->getCache('cache_pxa_pm_categories');
        $identifier = sha1('cache_cat_root_line' . implode(',', $idList) . ($removeGivenIdListFromResult ? '1' : '0'));

        $rootLine = $cache->get($identifier);
        if ($rootLine === false) {
            /** @var CategoryRepository $categoryRepository */
            $categoryRepository = MainUtility::getObjectManager()->get(CategoryRepository::class);
            $rootLine = $categoryRepository->getChildrenCategories($idList, $removeGivenIdListFromResult);

            // save in cache
            $cache->set($identifier, $rootLine);

            return $rootLine;
        }

        return $rootLine;
    }

    /**
     * Get all parents of category in ascending order
     *
     * @param Category $category
     * @param array $results
     * @return array
     */
    public static function getParentCategories(Category $category, array $results = []): array
    {
        /** @var Category $parent */
        $parent = $category->getParent();

        if (is_object($parent) && $parent->getUid() != $category->getUid()) {
            $results[] = $parent;
            $results = self::getParentCategories($parent, $results);
        }

        return $results;
    }
}
