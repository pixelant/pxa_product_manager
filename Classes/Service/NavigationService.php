<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\NavigationItem;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderService;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Generate categories navigation tree.
 */
class NavigationService
{
    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * @var bool
     */
    protected bool $expandAll = false;

    /**
     * @var Category
     */
    protected Category $rootCategory;

    /**
     * @var DemandInterface
     */
    protected DemandInterface $demandPrototype;

    /**
     * @var ServerRequest
     */
    protected ServerRequest $request;

    /**
     * @var NavigationItem[]
     */
    protected ?array $items = null;

    /**
     * Array of active categories.
     *
     * @var array
     */
    protected array $active = [];

    /**
     * UID of current category.
     *
     * @var int
     */
    protected int $current = 0;

    /**
     * @param Category $rootCategory
     * @param DemandInterface $demandPrototype
     */
    public function __construct(Category $rootCategory, DemandInterface $demandPrototype)
    {
        $this->rootCategory = $rootCategory;
        $this->demandPrototype = $demandPrototype;

        $this->request = $GLOBALS['TYPO3_REQUEST'];
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Generate navigation items tree.
     *
     * @return array
     */
    public function getItems(): array
    {
        if ($this->items === null) {
            $this->init();
        }

        return $this->items;
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
     * @return NavigationService
     */
    public function setExpandAll(bool $expandAll): self
    {
        $this->expandAll = $expandAll;

        return $this;
    }

    /**
     * Init items.
     */
    protected function init(): void
    {
        $this->setActiveFromRequest();
        $this->items = $this->generateTree($this->findSubCategories($this->rootCategory));
    }

    /**
     * Set active categories and current from request.
     */
    protected function setActiveFromRequest(): void
    {
        $prefix = UrlBuilderService::CATEGORY_ARGUMENT_START_WITH;
        $params = $this->request->getQueryParams()[UrlBuilderService::NAMESPACES] ?? [];

        if (isset($params['category'])) {
            $this->current = (int)$params['category'];
        }

        $this->active = array_map(
            'intval',
            array_filter($params, fn ($paramName) => strpos($paramName, $prefix) === 0, ARRAY_FILTER_USE_KEY)
        );
    }

    /**
     * Generate items tree.
     *
     * @param array $categories
     * @param int $iterator
     * @return array
     */
    protected function generateTree(array $categories, int $iterator = 0): array
    {
        $tree = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $isActive = in_array($category->getUid(), $this->active, true);
            $isCurrent = $this->current === $category->getUid();

            $item = GeneralUtility::makeInstance(
                NavigationItem::class,
                $category,
                $isActive,
                $isCurrent
            );
            $tree[] = $item;

            if ($isCurrent || $isActive || $this->expandAll) {
                $item->setSubItems(
                    $this->generateTree($this->findSubCategories($category), ++$iterator)
                );
            }

            if ($iterator > 99) {
                throw new \Exception('Reach maximum deep level while generate navigation', 1583844508799);
            }
        }

        return $tree;
    }

    /**
     * Find subcategories.
     *
     * @param Category $parent
     * @return array
     */
    protected function findSubCategories(Category $parent): array
    {
        $demand = $this->getDemandWithParent($parent);

        return $this->categoryRepository->findDemanded($demand)->toArray();
    }

    /**
     * Create demand clone with new parent.
     *
     * @param Category $parent
     * @return DemandInterface
     */
    protected function getDemandWithParent(Category $parent): DemandInterface
    {
        $demand = clone $this->demandPrototype;
        $demand->setParent($parent);

        return $demand;
    }
}
