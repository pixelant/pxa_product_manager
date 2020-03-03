<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Link;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class LinkBuilderService
 * @package Pixelant\PxaProductManager\Service\Link
 */
class UrlBuilderService implements UrlBuilderServiceInterface
{
    /**
     * Link constants
     */
    const CATEGORY_ARGUMENT_START_WITH = 'category_';
    const NAMESPACES = 'tx_pxaproductmanager_pi1';

    /**
     * @var TypoScriptFrontendController
     */
    protected TypoScriptFrontendController $tsfe;

    /**
     * @var int
     */
    protected int $languageUid = 0;

    /**
     * Initialize
     *
     * @param TypoScriptFrontendController|null $typoScriptFrontendController
     */
    public function __construct(TypoScriptFrontendController $typoScriptFrontendController = null)
    {
        $this->tsfe = $typoScriptFrontendController;
    }

    /**
     * Get product single view link
     *
     * @param int $pageUid Page Uid
     * @param int|Product $product Product object or UID
     * @param int|Category $category Category object or UID to override first product category
     * @param bool $excludeCategories Exclude categories from product single view url
     * @param bool $absolute Absolute link
     * @return string
     */
    public function buildForProduct(
        int $pageUid,
        $product,
        $category = null,
        bool $excludeCategories = false,
        bool $absolute = false
    ): string {
        $arguments = [];
        $productUid = is_object($product) ? $product->getUid() : (int)$product;
        if (!$excludeCategories) {
            $categoryUid = $this->getProductCategoryUid($product, $category);
            $arguments = $this->getCategoriesArguments($categoryUid);
        }
        $arguments['product'] = $productUid;

        return $this->buildUri($pageUid, 'show', $arguments, $absolute);
    }

    /**
     * Get link for category list view
     *
     * @param int $pageUid Page Uid
     * @param int|Category $category Category object or UID to generate url for list view
     * @param bool $absolute Absolute link
     * @return string
     */
    public function buildForCategory(
        int $pageUid,
        $category,
        bool $absolute = false
    ): string {
        $categoryUid = is_object($category) ? $category->getUid() : (int)$category;
        $arguments = $this->getCategoriesArguments($categoryUid);

        return $this->buildUri($pageUid, 'list', $arguments, $absolute);
    }

    /**
     * Build link for given arguments (For example from breadcrumbs)
     *
     * @param int $pageUid
     * @param array $arguments
     * @return string
     */
    public function buildForArguments(int $pageUid, array $arguments): string
    {
        $action = isset($arguments['product']) ? 'show' : 'list';

        return $this->buildUri($pageUid, $action, $arguments);
    }

    /**
     * @param int $languageUid
     */
    public function setLanguageUid(int $languageUid): void
    {
        $this->languageUid = $languageUid;
    }

    /**
     * @param int|Product $product
     * @param int|Category $category
     * @return int
     */
    protected function getProductCategoryUid($product, $category): int
    {
        if (is_object($category)) {
            return $category->getUid();
        }
        if ($category !== null) {
            return (int)$category;
        }
        if (is_object($product)) {
            $productCat = $product->getFirstCategory();
            return $productCat !== null ? $productCat->getUid() : 0;
        }

        $categories = ProductUtility::getProductCategoriesUids(intval($product));
        if (count($categories) > 0) {
            return $categories[0];
        }

        return 0;
    }

    /**
     * Generate link
     *
     * @param int $pageUid
     * @param string $action
     * @param array $arguments
     * @param bool $absolute
     * @return string
     */
    protected function buildUri(int $pageUid, string $action, array $arguments, bool $absolute = false): string
    {
        $arguments['action'] = $action;
        $arguments['controller'] = 'Product';

        $parameters = GeneralUtility::implodeArrayForUrl(
            static::NAMESPACES,
            $arguments
        );

        $confLink = [
            'parameter' => $pageUid,
            'language' => $this->languageUid,
            'useCacheHash' => true,
            'additionalParams' => $parameters,
            'forceAbsoluteUrl' => $absolute
        ];

        $signalArguments = [
            'conf' => &$confLink
        ];
        $this->emitSignal(__CLASS__, 'beforeBuildUri', $signalArguments);

        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = GeneralUtility::makeInstance(
            ContentObjectRenderer::class,
            $this->tsfe
        );
        return $contentObjectRenderer->typolink_URL($confLink);
    }

    /**
     * Get category tree arguments
     *
     * @param Category|null $category
     * @return array
     */
    protected function getCategoriesArguments(?Category $category): array
    {
        if ($category === null) {
            return [];
        }

        $arguments = [];
        $i = 0;
        $treeLine = $category->getParentsRootLineReverse();
        $lastCategory = array_pop($treeLine);

        foreach ($treeLine as $categoryItem) {
            $arguments[static::CATEGORY_ARGUMENT_START_WITH . $i++] = $category;
        }
        $arguments['category'] = $lastCategory;

        return $arguments;
    }

    /**
     * Check if FE request
     *
     * @return bool
     */
    protected function isFrontendRequestType(): bool
    {
        if (!defined('TYPO3_REQUESTTYPE')) {
            return false;
        }

        return (bool)(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_FE);
    }
}
