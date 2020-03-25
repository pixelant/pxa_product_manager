<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Category;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Traversable;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package Pixelant\PxaProductManager\Service\Category
 */
class TreeService
{
    /**
     * @var FrontendInterface
     */
    protected FrontendInterface $cache;

    /**
     * Constructor setting up the cache
     *
     * @param FrontendInterface|null $cache
     */
    public function __construct(FrontendInterface $cache = null)
    {
        $this->cache = $cache ?? GeneralUtility::makeInstance(CacheManager::class)->getCache('pm_cache_categories');
    }

    /**
     * Return array of given categories + all childer recursive
     *
     * @param array|Traversable $categories
     * @return array
     */
    public function childrenRecursive($categories): array
    {
        $this->validateType($categories);

        $cacheHash = $this->cacheHash($categories);
        if ($this->cache->has($cacheHash)) {
            return $this->cache->get($cacheHash);
        }

        $result = $this->fetchChildrenRecursive($categories);
        $this->cache->set($cacheHash, $result);

        return $result;
    }

    /**
     * Return hash
     *
     * @param array|Traversable $categories
     * @return string
     */
    protected function cacheHash($categories): string
    {
        $uids = array_map(fn(Category $category) => $category->getUid(), $categories);

        return sha1('pm_cache_categories' . implode(',', $uids));
    }

    /**
     * Fetch all children recursive
     *
     * @param array|Traversable $categories
     * @param array $result
     * @return array
     */
    protected function fetchChildrenRecursive($categories, array $result = []): array
    {
        /** @var Category $category */
        foreach ($categories as $category) {
            if (! in_array($category, $result, true)) {
                $result[] = $category;
                $result = $this->fetchChildrenRecursive($category->getSubCategories(), $result);
            }
        }
        return $result;
    }

    /**
     * Validate incoming type of categories
     *
     * @param $categories
     */
    protected function validateType($categories): void
    {
        if (! is_array($categories) && ! $categories instanceof Traversable) {
            throw new \InvalidArgumentException(
                sprintf('Expect categories to be array or Traversable, but got "%s"', gettype($categories)),
                1585128511867
            );
        }
    }
}
