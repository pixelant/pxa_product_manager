<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Create breadcrumbs array for UserFunc.
 */
class Breadcrumbs
{
    use CanCreateCollection;

    /**
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * Initialize repositories.
     *
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ProductRepository $productRepository = null,
        CategoryRepository $categoryRepository = null
    ) {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->request = $GLOBALS['TYPO3_REQUEST'];

        $this->productRepository = $productRepository ?? $objectManager->get(ProductRepository::class);
        $this->categoryRepository = $categoryRepository ?? $objectManager->get(CategoryRepository::class);
    }

    /**
     * @var array
     */
    protected array $items = [];

    /**
     * Build array of breadcrumbs for TypoScript user function.
     *
     * @return array
     */
    public function build(): array
    {
        if ($this->hasBreadcrumbs()) {
            $this->addProduct();
            $this->addCategories();
        }

        return array_reverse($this->items);
    }

    /**
     * Add product breadcrumbs if exist in arguments.
     */
    protected function addProduct(): void
    {
        $arguments = $this->getArguments();
        if (!isset($arguments['product'])) {
            return;
        }

        $arguments['action'] = 'show';
        $uid = (int)$arguments['product'];

        /** @var Product $product */
        $product = $this->productRepository->findByUid($uid);
        if ($product) {
            $this->items[] = [
                'title' => $product->getNavigationTitle(),
                'sys_language_uid' => $product->_getProperty('_languageUid'),
                '_OVERRIDE_HREF' => $this->url($arguments),
                'ITEM_STATE' => 'CUR',
            ];
        }
    }

    /**
     * Add categories breadcrumbs.
     */
    protected function addCategories(): void
    {
        $arguments = $this->filterCategoriesArguments($this->getArguments());
        // If not categories arguments nothing to do
        if (empty($arguments)) {
            return;
        }

        $uids = array_map('intval', $arguments);
        $categories = $this->collection(
            $this->categoryRepository->findByUids($uids)
        )->mapWithKeysOfProperty('uid')->toArray();

        foreach ($uids as $uid) {
            if (isset($categories[$uid])) {
                /** @var Category $category */
                $category = $categories[$uid];

                $this->items[] = [
                    'title' => $category->getNavigationTitle(),
                    'sys_language_uid' => $category->_getProperty('_languageUid'),
                    '_OVERRIDE_HREF' => $this->url($this->renameCategoriesArguments($arguments)),
                    // Could be current only if last
                    'ITEM_STATE' => empty($this->items) ? 'CUR' : 'NO',
                ];

                // Remove first argument
                array_shift($arguments);
            }
        }
    }

    /**
     * Rename categories argument for each breadcrumbs item url.
     *
     * @param array $arguments
     * @return array
     */
    protected function renameCategoriesArguments(array $arguments): array
    {
        $newArguments = [
            'category' => array_shift($arguments),
        ];

        $i = 0;
        foreach ($arguments as $argument) {
            $i++;
            $name = UrlBuilderService::CATEGORY_ARGUMENT_START_WITH . $i;
            $newArguments[$name] = $argument;
        }

        return $newArguments;
    }

    /**
     * Return only categories arguments from arguments.
     *
     * @param array $arguments
     * @return array
     */
    protected function filterCategoriesArguments(array $arguments): array
    {
        return array_filter($arguments, function ($key) {
            return strpos($key, 'category') === 0;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Return arguments of product manager.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return $this->request->getQueryParams()[UrlBuilderService::NAMESPACES];
    }

    /**
     * Check if product manager get parameters exist.
     *
     * @return bool
     */
    protected function hasBreadcrumbs(): bool
    {
        return array_key_exists(UrlBuilderService::NAMESPACES, $this->request->getQueryParams());
    }

    /**
     * Generate breadcrumbs URL for given arguments.
     *
     * @param array $arguments
     * @return string
     */
    protected function url(array $arguments): string
    {
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $typoLink = [
            'parameter' => $GLOBALS['TSFE']->id,
            'useCacheHash' => true,
            'additionalParams' => GeneralUtility::implodeArrayForUrl(UrlBuilderService::NAMESPACES, $arguments),
        ];

        return $cObj->typoLink_URL($typoLink);
    }
}
